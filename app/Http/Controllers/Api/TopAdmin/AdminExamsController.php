<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\ExamsRoadmapStep;
use App\Models\ExamsRoadmapFile;
use App\Models\ExamsSection;
use App\Models\ExamsQuestion;
use App\Models\ExamsQuestionOption;
use App\Models\ExamsQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminExamsController extends Controller
{
    public function getRoadmap($sub_type_id)
    {
        $steps = ExamsRoadmapStep::where('exam_sub_type_id', $sub_type_id)
            ->with(['sections.questions.options', 'sections.questions.answers', 'questions.options', 'questions.answers', 'files'])
            ->orderBy('order')
            ->get();
        return response()->json(['status' => true, 'data' => $steps]);
    }

    public function getStep($id)
    {
        $step = ExamsRoadmapStep::with(['sections.questions.options', 'sections.questions.answers', 'questions.options', 'questions.answers', 'files'])->findOrFail($id);
        return response()->json(['status' => true, 'data' => $step]);
    }

    public function storeStep(Request $request)
    {
        $data = $request->validate([
            'exam_sub_type_id' => 'required|exists:exam_sub_types,id',
            'pre_node_id' => 'nullable|exists:exams_roadmap_steps,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:resource,exam',
            'difficulty' => 'required|in:easy,medium,hard',
            'total_marks' => 'nullable|integer',
            'video_url' => 'nullable|string',
            'resource_files.*' => 'nullable|file|max:51200', 
            'color' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        return DB::transaction(function() use ($request, $data) {
            $step = ExamsRoadmapStep::create([
                'exam_sub_type_id' => $data['exam_sub_type_id'],
                'pre_node_id' => $data['pre_node_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'difficulty' => $data['difficulty'],
                'total_marks' => $data['total_marks'] ?? null,
                'video_url' => $data['video_url'] ?? null,
                'color' => $data['color'] ?? null,
                'order' => $data['order'] ?? 0
            ]);

            if ($request->hasFile('resource_files')) {
                foreach ($request->file('resource_files') as $file) {
                    $path = $file->store('exams/resources', 'public');
                    ExamsRoadmapFile::create([
                        'roadmap_step_id' => $step->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension()
                    ]);
                }
            }

            return response()->json(['status' => true, 'data' => $step->load('files')]);
        });
    }

    public function updateStep(Request $request, $id)
    {
        $step = ExamsRoadmapStep::findOrFail($id);
        
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:resource,exam',
            'difficulty' => 'required|in:easy,medium,hard',
            'total_marks' => 'nullable|integer',
            'video_url' => 'nullable|string',
            'resource_files.*' => 'nullable|file|max:51200',
            'pre_node_id' => 'nullable|exists:exams_roadmap_steps,id',
            'color' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        return DB::transaction(function() use ($request, $data, $step) {
            $step->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? $step->description,
                'type' => $data['type'],
                'difficulty' => $data['difficulty'],
                'total_marks' => $data['total_marks'] ?? $step->total_marks,
                'video_url' => $data['video_url'] ?? $step->video_url,
                'pre_node_id' => $data['pre_node_id'] ?? $step->pre_node_id,
                'color' => $data['color'] ?? $step->color,
                'order' => $data['order'] ?? $step->order
            ]);

            if ($request->hasFile('resource_files')) {
                foreach ($request->file('resource_files') as $file) {
                    $path = $file->store('exams/resources', 'public');
                    ExamsRoadmapFile::create([
                        'roadmap_step_id' => $step->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension()
                    ]);
                }
            }

            return response()->json(['status' => true, 'data' => $step->load('files')]);
        });
    }

    public function storeResources(Request $request, $step_id)
    {
        $step = ExamsRoadmapStep::findOrFail($step_id);
        
        $request->validate([
            'resource_files.*' => 'required|file|max:51200',
            'titles.*' => 'nullable|string',
            'descriptions.*' => 'nullable|string'
        ]);

        return DB::transaction(function() use ($request, $step) {
            if ($request->hasFile('resource_files')) {
                foreach ($request->file('resource_files') as $index => $file) {
                    $path = $file->store('exams/resources', 'public');
                    
                    $titles = $request->input('titles', []);
                    $descriptions = $request->input('descriptions', []);

                    ExamsRoadmapFile::create([
                        'roadmap_step_id' => $step->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'title' => $titles[$index] ?? null,
                        'description' => $descriptions[$index] ?? null
                    ]);
                }
            }
            return response()->json(['status' => true, 'data' => $step->load('files')]);
        });
    }

    public function deleteStepFile($file_id)
    {
        $file = ExamsRoadmapFile::findOrFail($file_id);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return response()->json(['status' => true, 'message' => 'File deleted']);
    }

    public function deleteStep($id)
    {
        $step = ExamsRoadmapStep::findOrFail($id);
        if ($step->video_url) Storage::disk('public')->delete($step->video_url);
        if ($step->file_path) Storage::disk('public')->delete($step->file_path);
        foreach ($step->questions as $q) {
            if ($q->audio_url) Storage::disk('public')->delete($q->audio_url);
        }
        $step->delete();
        return response()->json(['status' => true, 'message' => 'Node deleted successfully']);
    }

    public function storeQuestion(Request $request, $step_id)
    {
        $data = $request->validate([
            'section_id' => 'nullable|exists:exams_sections,id',
            'type' => 'required|in:multiple_choice,blank,short_answer,sound_to_write',
            'content' => 'required',
            'weight' => 'required|integer',
            'audio_file' => 'nullable|mimes:mp3,wav,m4a|max:20480' // 20MB
        ]);

        return DB::transaction(function() use ($request, $step_id, $data) {
            $audioPath = null;
            if ($request->hasFile('audio_file')) {
                $audioPath = $request->file('audio_file')->store('exams/audio', 'public');
            }

            $section = null;
            if (isset($data['section_id'])) {
                $section = ExamsSection::find($data['section_id']);
            }

            $question = ExamsQuestion::create([
                'roadmap_step_id' => $step_id,
                'section_id' => $data['section_id'] ?? null,
                'type' => $section ? $section->type : ($data['type'] ?? 'multiple_choice'),
                'content' => $request->content,
                'audio_url' => $audioPath,
                'weight' => $request->weight,
                'order' => $request->order ?? 0
            ]);

            if ($request->type === 'multiple_choice' && is_array($request->options)) {
                foreach ($request->options as $opt) {
                    ExamsQuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $opt['option_text'] ?? $opt['text'],
                        'is_correct' => $opt['is_correct'] ?? false
                    ]);
                }
            } else if (in_array($request->type, ['blank', 'short_answer', 'sound_to_write']) && is_array($request->answers)) {
                foreach ($request->answers as $ans) {
                    ExamsQuestionAnswer::create([
                        'question_id' => $question->id,
                        'answer_text' => $ans['answer_text'] ?? $ans['text']
                    ]);
                }
            }

            return response()->json(['status' => true, 'data' => $question->load(['options', 'answers'])]);
        });
    }

    public function deleteQuestion($id)
    {
        $question = ExamsQuestion::findOrFail($id);
        if ($question->audio_url) Storage::disk('public')->delete($question->audio_url);
        $question->delete();
        return response()->json(['status' => true, 'message' => 'Question deleted successfully']);
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = ExamsQuestion::findOrFail($id);
        
        $request->validate([
            'content' => 'required',
            'weight' => 'required|numeric'
        ]);

        if ($request->hasFile('audio_file')) {
            if ($question->audio_url) Storage::disk('public')->delete($question->audio_url);
            $question->audio_url = $request->file('audio_file')->store('exams/audio', 'public');
        }

        $updateData = [
            'content' => $request->content,
            'weight' => $request->weight,
        ];

        if ($request->section_id) {
            $section = ExamsSection::find($request->section_id);
            if ($section) {
                $updateData['type'] = $section->type;
                $updateData['section_id'] = $request->section_id;
            }
        } elseif ($request->type) {
            $updateData['type'] = $request->type;
        }

        $question->update($updateData);

        // Replace options/answers
        return DB::transaction(function () use ($request, $question) {
            if ($question->type === 'multiple_choice') {
                $question->options()->delete();
                $options = is_string($request->options) ? json_decode($request->options, true) : $request->options;
                if (is_array($options)) {
                    foreach ($options as $opt) {
                        ExamsQuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $opt['option_text'] ?? $opt['text'],
                            'is_correct' => $opt['is_correct'] ?? false
                        ]);
                    }
                }
            } else {
                $question->answers()->delete();
                $answers = is_string($request->answers) ? json_decode($request->answers, true) : $request->answers;
                if (is_array($answers)) {
                    foreach ($answers as $ans) {
                        ExamsQuestionAnswer::create([
                            'question_id' => $question->id,
                            'answer_text' => $ans['answer_text'] ?? $ans['text']
                        ]);
                    }
                }
            }
            return response()->json(['status' => true, 'data' => $question->load(['options', 'answers'])]);
        });
    }
    public function storeSection(Request $request, $step_id)
    {
        $step = ExamsRoadmapStep::findOrFail($step_id);
        $data = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'marks' => 'required|integer',
            'order' => 'nullable|integer'
        ]);

        $section = $step->sections()->create([
            'title' => $data['title'],
            'type' => $data['type'],
            'marks' => $data['marks'],
            'order' => $data['order'] ?? 0
        ]);
        return response()->json(['status' => true, 'data' => $section]);
    }

    public function updateSection(Request $request, $id)
    {
        $section = ExamsSection::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'marks' => 'required|integer',
            'order' => 'nullable|integer'
        ]);

        $section->update($data);
        return response()->json(['status' => true, 'data' => $section]);
    }

    public function deleteSection($id)
    {
        $section = ExamsSection::findOrFail($id);
        $section->delete();
        return response()->json(['status' => true]);
    }
}

