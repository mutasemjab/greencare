<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ReportTemplate;
use App\Models\ReportSection;
use App\Models\ReportField;
use App\Models\ReportFieldOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:report-template-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:report-template-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:report-template-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:report-template-delete', ['only' => ['destroy']]);
    }

    public function searchTemplates(Request $request)
    {
        $search = $request->get('search');
        $perPage = 10;

        $query = ReportTemplate::where('active', true); // Assuming you have an active field

        // Search by template name or description
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $templates = $query->orderBy('name')
                        ->paginate($perPage);

        // Transform data for Select2
        $data = $templates->getCollection()->map(function($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'sections_count' => $template->sections()->count() ?? 0,
                'fields_count' => $template->fields()->count() ?? 0,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $templates->currentPage(),
            'last_page' => $templates->lastPage(),
            'per_page' => $templates->perPage(),
            'total' => $templates->total(),
        ]);
    }
    /**
     * Display a listing of report templates
     */
    public function index(Request $request)
    {
        $query = ReportTemplate::with('sections');

        // Filter by created_for
        if ($request->has('created_for') && !empty($request->created_for)) {
            $query->where('created_for', $request->created_for);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_en', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.report-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new report template
     */
    public function create()
    {
        return view('admin.report-templates.create');
    }

    /**
     * Store a newly created report template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'created_for' => 'required|in:doctor,nurse',
            'report_type' => 'required|in:initial_setup,recurring',
            'frequency' => 'required_if:report_type,recurring|nullable|in:daily,weekly,monthly',
            'sections' => 'required|array|min:1',
            'sections.*.title_en' => 'required|string|max:255',
            'sections.*.title_ar' => 'required|string|max:255',
            'sections.*.fields' => 'required|array|min:1',
            'sections.*.fields.*.label_en' => 'required|string|max:255',
            'sections.*.fields.*.label_ar' => 'required|string|max:255',
            'sections.*.fields.*.input_type' => 'required|in:text,textarea,number,date,select,radio,checkbox,boolean,gender',
            'sections.*.fields.*.required' => 'boolean',
            'sections.*.fields.*.options' => 'array',
            'sections.*.fields.*.options.*.value_en' => 'required_with:sections.*.fields.*.options|string|max:255',
            'sections.*.fields.*.options.*.value_ar' => 'required_with:sections.*.fields.*.options|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create template with new fields
            $template = ReportTemplate::create([
                'title_en' => $request->title_en,
                'title_ar' => $request->title_ar,
                'created_for' => $request->created_for,
                'report_type' => $request->report_type,
                'frequency' => $request->report_type === 'recurring' ? $request->frequency : 'one_time',
            ]);

            // Rest of your existing code remains the same...
            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = ReportSection::create([
                    'report_template_id' => $template->id,
                    'title_en' => $sectionData['title_en'],
                    'title_ar' => $sectionData['title_ar'],
                    'order' => $sectionIndex + 1,
                ]);

                foreach ($sectionData['fields'] as $fieldData) {
                    $field = ReportField::create([
                        'report_section_id' => $section->id,
                        'label_en' => $fieldData['label_en'],
                        'label_ar' => $fieldData['label_ar'],
                        'input_type' => $fieldData['input_type'],
                        'required' => isset($fieldData['required']) ? (bool)$fieldData['required'] : false,
                    ]);

                    if (isset($fieldData['options']) && is_array($fieldData['options'])) {
                        foreach ($fieldData['options'] as $optionData) {
                            ReportFieldOption::create([
                                'report_field_id' => $field->id,
                                'value_en' => $optionData['value_en'],
                                'value_ar' => $optionData['value_ar'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('report-templates.index')
                ->with('success', __('messages.report_template_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_creating_report_template'))
                ->withInput();
        }
    }

    /**
     * Display the specified report template
     */
    public function show(ReportTemplate $reportTemplate)
    {
        $reportTemplate->load(['sections.fields.options']);
        return view('admin.report-templates.show', compact('reportTemplate'));
    }

    /**
     * Show the form for editing the specified report template
     */
    public function edit(ReportTemplate $reportTemplate)
    {
        $reportTemplate->load(['sections.fields.options']);
        return view('admin.report-templates.edit', compact('reportTemplate'));
    }

    /**
     * Update the specified report template
     */
    public function update(Request $request, ReportTemplate $reportTemplate)
    {
        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'created_for' => 'required|in:doctor,nurse',
            'report_type' => 'required|in:initial_setup,recurring',
            'frequency' => 'required_if:report_type,recurring|nullable|in:daily,weekly,monthly',
            'sections' => 'required|array|min:1',
            'sections.*.title_en' => 'required|string|max:255',
            'sections.*.title_ar' => 'required|string|max:255',
            'sections.*.fields' => 'required|array|min:1',
            'sections.*.fields.*.label_en' => 'required|string|max:255',
            'sections.*.fields.*.label_ar' => 'required|string|max:255',
            'sections.*.fields.*.input_type' => 'required|in:text,textarea,number,date,select,radio,checkbox,boolean,gender',
            'sections.*.fields.*.required' => 'boolean',
            'sections.*.fields.*.options' => 'array',
            'sections.*.fields.*.options.*.value_en' => 'required_with:sections.*.fields.*.options|string|max:255',
            'sections.*.fields.*.options.*.value_ar' => 'required_with:sections.*.fields.*.options|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update template
            $reportTemplate->update([
                'title_en' => $request->title_en,
                'title_ar' => $request->title_ar,
                'created_for' => $request->created_for,
                'report_type' => $request->report_type,
                'frequency' => $request->report_type === 'recurring' ? $request->frequency : 'one_time',
            ]);

            // Delete existing sections (cascade will handle fields and options)
            $reportTemplate->sections()->delete();

            // Create new sections and fields
            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = ReportSection::create([
                    'report_template_id' => $reportTemplate->id,
                    'title_en' => $sectionData['title_en'],
                    'title_ar' => $sectionData['title_ar'],
                    'order' => $sectionIndex + 1,
                ]);

                foreach ($sectionData['fields'] as $fieldData) {
                    $field = ReportField::create([
                        'report_section_id' => $section->id,
                        'label_en' => $fieldData['label_en'],
                        'label_ar' => $fieldData['label_ar'],
                        'input_type' => $fieldData['input_type'],
                        'required' => isset($fieldData['required']) ? (bool)$fieldData['required'] : false,
                    ]);

                    // Create options if field type needs them
                    if (isset($fieldData['options']) && is_array($fieldData['options'])) {
                        foreach ($fieldData['options'] as $optionData) {
                            ReportFieldOption::create([
                                'report_field_id' => $field->id,
                                'value_en' => $optionData['value_en'],
                                'value_ar' => $optionData['value_ar'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('report-templates.index')
                ->with('success', __('messages.report_template_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_updating_report_template'))
                ->withInput();
        }
    }

    /**
     * Remove the specified report template
     */
    public function destroy(ReportTemplate $reportTemplate)
    {
        try {
            $reportTemplate->delete();
            return redirect()->route('report-templates.index')
                ->with('success', __('messages.report_template_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_report_template'));
        }
    }

    /**
     * Duplicate a report template
     */
    public function duplicate(ReportTemplate $reportTemplate)
    {
        DB::beginTransaction();
        try {
            $newTemplate = ReportTemplate::create([
                'title_en' => $reportTemplate->title_en . ' (Copy)',
                'title_ar' => $reportTemplate->title_ar . ' (نسخة)',
                'created_for' => $reportTemplate->created_for,
            ]);

            foreach ($reportTemplate->sections as $section) {
                $newSection = ReportSection::create([
                    'report_template_id' => $newTemplate->id,
                    'title_en' => $section->title_en,
                    'title_ar' => $section->title_ar,
                    'order' => $section->order,
                ]);

                foreach ($section->fields as $field) {
                    $newField = ReportField::create([
                        'report_section_id' => $newSection->id,
                        'label_en' => $field->label_en,
                        'label_ar' => $field->label_ar,
                        'input_type' => $field->input_type,
                        'required' => $field->required,
                    ]);

                    foreach ($field->options as $option) {
                        ReportFieldOption::create([
                            'report_field_id' => $newField->id,
                            'value_en' => $option->value_en,
                            'value_ar' => $option->value_ar,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('report-templates.edit', $newTemplate)
                ->with('success', __('messages.report_template_duplicated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_duplicating_report_template'));
        }
    }

    /**
     * Get templates for AJAX calls
     */
    public function getTemplates(Request $request)
    {
        $query = ReportTemplate::query();

        if ($request->has('created_for')) {
            $query->where('created_for', $request->created_for);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_en', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%");
            });
        }

        $templates = $query->select('id', 'title_en', 'title_ar', 'created_for')
                          ->orderBy('title_en')
                          ->get();

        return response()->json($templates);
    }
}