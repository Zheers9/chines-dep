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
use ZipArchive;

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

    // ── Word Upload: Analyze ──────────────────────────────────────────────────

    private function readDocxText(string $path): string
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) return '';
        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();
        if ($xml === '') return '';
        // Each paragraph and table row/cell gets its own line
        $xml = str_replace(['</w:p>', '</w:tr>', '</w:tc>'], "\n", $xml);
        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = preg_replace("/[ \t]+/", " ", $text);        // collapse multiple spaces/tabs
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", (string)$text);
        return trim((string)$text);
    }

    private function makeQuestion(array $section, string $content): array
    {
        $isTF = preg_match('/判断|正.*误|T.*F/u', $section['title'] ?? '') === 1;
        $options = [];
        if ($section['type'] === 'multiple_choice' && $isTF) {
            $options = [
                ['text' => 'T', 'is_correct' => false],
                ['text' => 'F', 'is_correct' => false],
            ];
        }
        return [
            'type'    => $section['type'],
            'content' => $content,
            'weight'  => 5,
            'options' => $options,
            'answers' => [],
        ];
    }

    private function detectSectionType(string $line): string
    {
        // Chinese keywords
        if (preg_match('/判断|正.*误|T.*F/u', $line))            return 'multiple_choice';
        if (preg_match('/选择/u', $line))                         return 'multiple_choice';
        if (preg_match('/阅读|理解|短文|对话|回答/u', $line))     return 'short_answer';
        if (preg_match('/写作|作文/u', $line))                    return 'short_answer';
        // English keywords
        if (preg_match('/\b(read|passage|answer the questions)\b/i', $line))  return 'short_answer';
        if (preg_match('/\b(choose|select|multiple.choice)\b/i', $line))      return 'multiple_choice';
        if (preg_match('/\b(fill|blank|complete|change|match|make|sentence|dialogue)\b/i', $line)) return 'blank';
        if (preg_match('/\b(write|essay|composition)\b/i', $line))            return 'short_answer';
        return 'blank';
    }

    private function detectSubtype(string $line, string $type): string
    {
        if (preg_match('/写作|作文|\bwrite|\bessay|\bcomposition/iu', $line)) return 'writing';
        if (preg_match('/阅读|理解|短文|对话|\bread|\bpassage/iu', $line))    return 'reading';
        if ($type === 'short_answer') return 'reading';
        return 'other';
    }

    private function isSectionHeader(string $line): bool
    {
        // 1. Contains Chinese marks notation （10分）
        if (preg_match('/[（(]\d+分[）)]/u', $line)) return true;
        // 2. Starts with Chinese ordinal + any separator
        if (preg_match('/^[一二三四五六七八九十]+\s*[^\p{Han}\p{L}\d\s\n]/u', $line)) return true;
        // 3. Starts with digit + separator AND contains English instruction keywords
        if (preg_match('/^\d+\s*[,，、.\)）]\s*/u', $line) &&
            preg_match('/\b(complete|change|read|fill|make|match|write|answer|choose|translate|listen|sentence|phrase|blank|dialogue)\b/i', $line)) {
            return true;
        }
        // 4. Pure English instruction starting with capital (e.g. "Match words from part 1...")
        if (preg_match('/^[A-Z][a-z]+ /u', $line) &&
            preg_match('/\b(complete|change|read|fill|make|match|write|answer|choose|translate|sentence|phrase|blank|dialogue)\b/i', $line)) {
            return true;
        }
        return false;
    }

    private function parseDocxSections(string $text): array
    {
        $rawLines = preg_split("/\n/", $text) ?: [];

        // ── Pre-pass: join orphan numeral lines with next line ────────────────
        // Handles Word files where "一、" and "Title（N分）" are separate paragraphs
        $lines = [];
        $pending = null;
        foreach ($rawLines as $raw) {
            $l = trim((string)$raw);
            if ($l === '') {
                if ($pending !== null) { $lines[] = $pending; $pending = null; }
                continue;
            }
            if ($pending !== null) {
                $lines[] = $pending . $l;
                $pending = null;
            } elseif (preg_match('/^[一二三四五六七八九十]+\s*[^\p{Han}\p{L}\d\s]?\s*$/u', $l)) {
                // Line is just "一、" or "一" with no title → hold and combine with next
                $pending = $l;
            } else {
                $lines[] = $l;
            }
        }
        if ($pending !== null) $lines[] = $pending;

        // ── Main parse ───────────────────────────────────────────────────────
        $sections       = [];
        $currentSection = null;
        $currentQuestion = null;

        $commitQ = function () use (&$currentQuestion, &$currentSection) {
            if (!$currentQuestion) return;
            $content = trim((string)($currentQuestion['content'] ?? ''));
            if ($content === '') { $currentQuestion = null; return; }
            $currentQuestion['content'] = $content;
            if ($currentSection !== null) {
                $currentSection['questions'][] = $currentQuestion;
            }
            $currentQuestion = null;
        };

        $commitS = function () use (&$currentSection, &$sections, &$commitQ) {
            $commitQ();
            if ($currentSection === null) return;
            $cnt = count($currentSection['questions']);
            if ($cnt > 0 && $currentSection['marks'] > 0) {
                $w = max(1, (int)round($currentSection['marks'] / $cnt));
                foreach ($currentSection['questions'] as &$q) { $q['weight'] = $w; }
                unset($q);
            }
            $sections[] = $currentSection;
            $currentSection = null;
        };

        foreach ($lines as $line) {
            if ($line === '') continue;

            // ── Helper: build a new section from this line ───────────────────
            $makeSection = function (string $l) {
                $marks = 0;
                if (preg_match('/[（(](\d+)分[）)]/u', $l, $mm)) {
                    $marks = (int)$mm[1];
                } elseif (preg_match('/[（(](\d+)[）)]/u', $l, $mm)) {
                    $marks = (int)$mm[1];
                }
                $type    = $this->detectSectionType($l);
                $subtype = $this->detectSubtype($l, $type);
                return ['title' => $l, 'marks' => $marks, 'type' => $type,
                        'passage' => '', '_sub' => $subtype, 'questions' => []];
            };

            // ── Section header: any line with （N分） or Chinese ordinal ────────
            if ($this->isSectionHeader($line)) {
                $commitS();
                $currentSection = $makeSection($line);
                continue;
            }

            if ($currentSection === null) continue;

            // ── Numbered sub-question: 1、 1. 1) 1） 1, 1， （1） (1) ──────────
            $numbered = false;
            $numContent = '';
            if (preg_match('/^[（(](\d+)[）)]\s*(.*)$/u', $line, $m)) {
                $numbered = true;
                $numContent = $m[2];
            } elseif (preg_match('/^(\d+)\s*[、\.\)），,]\s*(.*)$/u', $line, $m)) {
                $numbered = true;
                $numContent = $m[2];
            }
            if ($numbered) {
                $commitQ();
                $content = trim((string)(preg_replace('/[（(][\s_]+[）)]/u', '', $numContent) ?? $numContent));
                $currentQuestion = $this->makeQuestion($currentSection, $content);
                continue;
            }

            // ── Skip pure labels like "Questions" or "Questions(3)" ──────────
            if (preg_match('/^Questions?\s*(\(\d+\))?\s*$/i', $line)) {
                continue;
            }

            // ── Line classifiers ─────────────────────────────────────────────
            $isDialogue    = (bool)preg_match('/^[\p{Han}]{1,8}[：:]/u', $line);
            $isInstruction = (bool)preg_match('/[：:]$/u', $line) && mb_strlen($line, 'UTF-8') > 15;
            $isSingleLetter= (bool)preg_match('/^[A-Za-z]$/u', $line);
            $isLong        = mb_strlen($line, 'UTF-8') > 80;
            $isPassage     = $isDialogue || $isInstruction || $isSingleLetter || $isLong;

            // ── Inline MCQ options: "A gè B jiè C zhú" → parse into options ──
            if ($currentQuestion !== null &&
                preg_match('/^\s*A\s+\S+\s+B\s+\S+\s+C\s+\S+/i', $line)) {
                if (preg_match_all('/([A-D])\s+(\S+)/i', $line, $optMatches, PREG_SET_ORDER)) {
                    foreach ($optMatches as $om) {
                        $currentQuestion['options'][] = ['text' => trim($om[2]), 'is_correct' => false];
                    }
                    $currentQuestion['type'] = 'multiple_choice';
                }
                continue;
            }

            // ── Skip Roman numeral sub-labels like Ⅰ, Ⅱ ──────────────────────
            if (preg_match('/^[ⅠⅡⅢⅣⅤⅠⅡⅢⅣⅤIiIiVv]{1,3}\s*$/u', $line)) {
                $currentSection['passage'] = trim(($currentSection['passage'] ?? '') . "\n" . $line);
                continue;
            }

            // ── Option lines A) B) only when question is active ──────────────
            if ($currentQuestion !== null && !$isPassage &&
                preg_match('/^[（(]?([A-Da-d])[）)]\s*[、.]?\s*(.+)$/u', $line, $m)) {
                $currentQuestion['options'][] = ['text' => trim($m[2]), 'is_correct' => false];
                $currentQuestion['type'] = 'multiple_choice';
                continue;
            }

            // ── Answer label ─────────────────────────────────────────────────
            if ($currentQuestion !== null &&
                preg_match('/^(?:answer|答案|正确答案)\s*[:：]\s*(.+)$/iu', $line, $m)) {
                $ans = trim($m[1]);
                if ($currentQuestion['type'] === 'multiple_choice') {
                    $map = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
                    $upper = strtoupper($ans);
                    if (isset($map[$upper]) && isset($currentQuestion['options'][$map[$upper]])) {
                        foreach ($currentQuestion['options'] as $i => $_) {
                            $currentQuestion['options'][$i]['is_correct'] = ($i === $map[$upper]);
                        }
                    }
                } else {
                    foreach (preg_split('/\s*[|\/,]\s*/u', $ans) ?: [] as $p) {
                        if (trim($p) !== '') $currentQuestion['answers'][] = ['text' => trim($p)];
                    }
                }
                continue;
            }

            $sub = $currentSection['_sub'] ?? 'other';

            // ── Reading sections: lines ending with ？ are questions, rest is passage
            if ($sub === 'reading') {
                if (preg_match('/[？?]\s*$/u', $line)) {
                    // It's a question (may have lost its number prefix from Word auto-lists)
                    $commitQ();
                    $cleanContent = trim((string)(preg_replace('/[（(][\s_]+[）)]/u', '', $line) ?? $line));
                    if ($cleanContent !== '') {
                        $currentQuestion = $this->makeQuestion($currentSection, $cleanContent);
                    }
                } else {
                    $currentSection['passage'] = trim(($currentSection['passage'] ?? '') . "\n" . $line);
                }
                continue;
            }

            // ── Other sections: passage text → collect into section.passage ───
            if ($isPassage) {
                $currentSection['passage'] = trim(($currentSection['passage'] ?? '') . "\n" . $line);
                continue;
            }

            $cleanLine = trim((string)(preg_replace('/[（(][\s_]+[）)]/u', '', $line) ?? $line));
            if ($cleanLine === '') continue;

            // ── No active question ───────────────────────────────────────────
            if ($currentQuestion === null) {
                $currentQuestion = $this->makeQuestion($currentSection, $cleanLine);
                continue;
            }

            // ── Active question, nothing else matched ────────────────────────
            $hasContent = trim($currentQuestion['content']) !== '';
            if (!$hasContent) {
                $currentQuestion['content'] = $cleanLine;
            } else {
                // Each new non-passage line = a new sub-question
                $commitQ();
                $currentQuestion = $this->makeQuestion($currentSection, $cleanLine);
            }
        }

        $commitS();

        foreach ($sections as &$sec) {
            unset($sec['_sub']);
            $sec['passage'] = trim($sec['passage'] ?? '');
        }
        unset($sec);

        return $sections;
    }

    public function analyzeWord(Request $request, $step_id)
    {
        ExamsRoadmapStep::findOrFail($step_id);
        $request->validate(['word_file' => 'required|file|mimes:docx|max:51200']);

        $text = $this->readDocxText($request->file('word_file')->getRealPath());
        if ($text === '') {
            return response()->json(['status' => false, 'message' => 'Could not read Word file content.', 'data' => []], 422);
        }

        // Build debug: first 80 non-empty lines so we can see raw extraction
        $debugLines = [];
        foreach (preg_split("/\n/", $text) ?: [] as $l) {
            $l = trim($l);
            if ($l !== '') $debugLines[] = $l;
            if (count($debugLines) >= 80) break;
        }

        return response()->json([
            'status'      => true,
            'message'     => 'Analyzed successfully.',
            'data'        => $this->parseDocxSections($text),
            'debug_lines' => $debugLines,   // remove this after fixing
        ]);
    }

    public function storeAnalyzed(Request $request, $step_id)
    {
        ExamsRoadmapStep::findOrFail($step_id);
        $request->validate([
            'sections'                                   => 'required|array|min:1',
            'sections.*.title'                           => 'required|string',
            'sections.*.marks'                           => 'nullable|integer|min:0',
            'sections.*.type'                            => 'required|in:multiple_choice,blank,short_answer,sound_to_write',
            'sections.*.passage'                         => 'nullable|string',
            'sections.*.questions'                       => 'nullable|array',
            'sections.*.questions.*.type'                => 'required|in:multiple_choice,blank,short_answer,sound_to_write',
            'sections.*.questions.*.content'             => 'required|string',
            'sections.*.questions.*.weight'              => 'nullable|integer|min:1',
            'sections.*.questions.*.options'             => 'nullable|array',
            'sections.*.questions.*.options.*.text'      => 'nullable|string',
            'sections.*.questions.*.options.*.is_correct'=> 'nullable|boolean',
            'sections.*.questions.*.answers'             => 'nullable|array',
            'sections.*.questions.*.answers.*.text'      => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $step_id) {
            foreach ($request->input('sections', []) as $si => $secData) {
                $section = ExamsSection::create([
                    'roadmap_step_id' => $step_id,
                    'title'           => $secData['title'],
                    'type'            => $secData['type'],
                    'marks'           => $secData['marks'] ?? 10,
                    'passage'         => $secData['passage'] ?? null,
                    'order'           => $si + 1,
                ]);
                foreach ($secData['questions'] ?? [] as $qi => $item) {
                    $question = ExamsQuestion::create([
                        'roadmap_step_id' => $step_id,
                        'section_id'      => $section->id,
                        'type'            => $item['type'],
                        'content'         => $item['content'],
                        'weight'          => $item['weight'] ?? 5,
                        'order'           => $qi + 1,
                    ]);
                    if ($item['type'] === 'multiple_choice') {
                        foreach ($item['options'] ?? [] as $opt) {
                            ExamsQuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => $opt['text'] ?? '',
                                'is_correct'  => (bool)($opt['is_correct'] ?? false),
                            ]);
                        }
                    } else {
                        foreach ($item['answers'] ?? [] as $ans) {
                            ExamsQuestionAnswer::create([
                                'question_id' => $question->id,
                                'answer_text' => $ans['text'] ?? '',
                            ]);
                        }
                    }
                }
            }
        });

        return response()->json(['status' => true, 'message' => 'Questions saved successfully.']);
    }
}

