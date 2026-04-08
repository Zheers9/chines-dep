<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\ExamQuestion;
use App\Models\RoadmapStep;
use App\Traits\UploadsFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamQuestionController extends Controller
{
    use UploadsFile;

    public function indexByStep(string $stepId)
    {
        $questions = ExamQuestion::query()
            ->where('roadmap_step_id', $stepId)
            ->with(['options', 'answers'])
            ->orderBy('order')
            ->get();

        return response()->json(['data' => $questions]);
    }

    public function store(Request $request, string $stepId)
    {
        RoadmapStep::findOrFail($stepId);

        $validated = $request->validate([
            'type' => ['required', 'in:multiple_choice,blank,true_false,voice'],
            'content' => ['required', 'string'],
            'weight' => ['nullable', 'integer', 'min:1'],
            'options' => ['nullable'],
            'answers' => ['nullable'],
            'audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:15360'],
        ]);

        if ($request->hasFile('audio_file')) {
            $stored = $this->storeUploadedFile($request->file('audio_file'), 'questions/audio');
            $validated['audio_url'] = $stored['path'];
        }

        $question = DB::transaction(function () use ($validated, $stepId) {
            $question = ExamQuestion::create([
                'roadmap_step_id' => (int) $stepId,
                'type' => $validated['type'],
                'content' => $validated['content'],
                'audio_url' => $validated['audio_url'] ?? null,
                'weight' => $validated['weight'] ?? 5,
                'order' => ExamQuestion::query()->where('roadmap_step_id', $stepId)->count(),
            ]);

            $options = $this->decodeJsonArray($validated['options'] ?? null);
            $answers = $this->decodeJsonArray($validated['answers'] ?? null);

            if ($validated['type'] === 'multiple_choice' || $validated['type'] === 'true_false') {
                foreach ($options as $option) {
                    $text = trim((string) ($option['text'] ?? ''));
                    if ($text === '') {
                        continue;
                    }
                    $question->options()->create([
                        'option_text' => $text,
                        'is_correct' => (bool) ($option['is_correct'] ?? false),
                    ]);
                }
            } else {
                foreach ($answers as $answer) {
                    $text = trim((string) ($answer['text'] ?? ''));
                    if ($text === '') {
                        continue;
                    }
                    $question->answers()->create(['answer_text' => $text]);
                }
            }

            return $question->load(['options', 'answers']);
        });

        return response()->json(['message' => 'Question created successfully', 'data' => $question]);
    }

    public function destroy(string $id)
    {
        $question = ExamQuestion::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Question deleted successfully']);
    }

    public function analyzeWord(Request $request, string $stepId)
    {
        RoadmapStep::findOrFail($stepId);
        $request->validate([
            'word_file' => ['required', 'file', 'mimes:docx', 'max:10240'],
        ]);

        $text = $this->extractDocxText($request->file('word_file')->getRealPath());
        $questions = $this->parseQuestionsFromText($text);

        return response()->json([
            'message' => 'Word file analyzed successfully',
            'data' => $questions,
        ]);
    }

    public function storeAnalyzed(Request $request, string $stepId)
    {
        RoadmapStep::findOrFail($stepId);
        $validated = $request->validate([
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.type' => ['required', 'in:multiple_choice,blank,true_false,voice'],
            'questions.*.content' => ['required', 'string'],
            'questions.*.weight' => ['nullable', 'integer', 'min:1'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.answers' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($validated, $stepId) {
            $baseOrder = ExamQuestion::query()->where('roadmap_step_id', $stepId)->count();
            foreach ($validated['questions'] as $index => $item) {
                $question = ExamQuestion::create([
                    'roadmap_step_id' => (int) $stepId,
                    'type' => $item['type'],
                    'content' => $item['content'],
                    'weight' => $item['weight'] ?? 5,
                    'order' => $baseOrder + $index,
                ]);

                if (in_array($item['type'], ['multiple_choice', 'true_false'], true)) {
                    foreach (($item['options'] ?? []) as $option) {
                        $text = trim((string) ($option['text'] ?? ''));
                        if ($text === '') {
                            continue;
                        }
                        $question->options()->create([
                            'option_text' => $text,
                            'is_correct' => (bool) ($option['is_correct'] ?? false),
                        ]);
                    }
                } else {
                    foreach (($item['answers'] ?? []) as $answer) {
                        $text = trim((string) ($answer['text'] ?? ''));
                        if ($text === '') {
                            continue;
                        }
                        $question->answers()->create(['answer_text' => $text]);
                    }
                }
            }
        });

        return response()->json(['message' => 'Analyzed questions saved successfully']);
    }

    private function decodeJsonArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value) || trim($value) === '') {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function extractDocxText(string $path): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();
        if ($xml === '') {
            return '';
        }

        $xml = str_replace(['</w:p>', '</w:tr>'], ["\n", "\n"], $xml);
        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
    }

    private function parseQuestionsFromText(string $text): array
    {
        $blocks = preg_split("/\n\s*\n/", $text) ?: [];
        $questions = [];

        foreach ($blocks as $block) {
            $lines = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $block) ?: [])));
            if (count($lines) < 2) {
                continue;
            }

            $type = $this->mapType((string) ($lines[0] ?? ''));
            $content = '';
            $options = [];
            $answers = [];

            foreach ($lines as $line) {
                if (preg_match('/^(question|q|prompt|السؤال|题目)\s*[:：]\s*(.+)$/iu', $line, $m)) {
                    $content = trim($m[2]);
                    continue;
                }
                if (preg_match('/^(option|choice|خيار|选项)\s*[A-D]?\s*[:：]\s*(.+)$/iu', $line, $m)) {
                    $options[] = ['text' => trim($m[2]), 'is_correct' => false];
                    continue;
                }
                if (preg_match('/^(answer|correct|الإجابة|答案)\s*[:：]\s*(.+)$/iu', $line, $m)) {
                    $ans = trim($m[2]);
                    if ($type === 'multiple_choice' || $type === 'true_false') {
                        foreach ($options as &$opt) {
                            if (strcasecmp($opt['text'], $ans) === 0) {
                                $opt['is_correct'] = true;
                            }
                        }
                        unset($opt);
                    } else {
                        $answers[] = ['text' => $ans];
                    }
                }
            }

            if ($content === '') {
                $content = $lines[1] ?? '';
            }
            if ($content === '') {
                continue;
            }

            if ($type === 'true_false' && count($options) === 0) {
                $options = [
                    ['text' => 'True', 'is_correct' => false],
                    ['text' => 'False', 'is_correct' => false],
                ];
            }
            if (($type === 'blank' || $type === 'voice') && count($answers) === 0) {
                $answers = [['text' => '']];
            }

            $questions[] = [
                'type' => $type,
                'content' => $content,
                'weight' => 5,
                'options' => $options,
                'answers' => $answers,
            ];
        }

        return $questions;
    }

    private function mapType(string $rawType): string
    {
        $value = mb_strtolower(trim($rawType));
        if (str_contains($value, 'multiple') || str_contains($value, 'choose') || str_contains($value, 'choice') || str_contains($value, 'اختيار') || str_contains($value, '选择')) {
            return 'multiple_choice';
        }
        if (str_contains($value, 'true') || str_contains($value, 'false') || str_contains($value, 'صح') || str_contains($value, 'خطأ') || str_contains($value, '判断')) {
            return 'true_false';
        }
        if (str_contains($value, 'voice') || str_contains($value, 'audio') || str_contains($value, 'صوت') || str_contains($value, '听')) {
            return 'voice';
        }

        return 'blank';
    }
}
