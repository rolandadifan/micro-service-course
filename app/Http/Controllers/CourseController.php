<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\MyCourse;
use App\Models\Review;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $courses = Course::with(['mentor'])->get();
        $courses = Course::query();

        $q = $request->query('q');
        $status = $request->query('status');

        $courses->when($q, function ($query) use ($q) {
            return $query->whereRaw("name LIKE '%" . strtolower($q) . "%'");
        });

        $courses->when($status, function ($query) use ($status) {
            return $query->where('status', '=', $status);
        });

        return response()->json([
            'status' => 'success',
            'data' => $courses->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'certificate' => 'required|boolean',
            'thumbnail' => 'string|url',
            'type' => 'string|in:free,premium',
            'status' => 'string|in:draft,published',
            'price' => 'integer',
            'lavel' => 'required|in:alllavel,beginer,intermediate,advance',
            'description' => 'string',
            'mentor_id' => 'required|integer'
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $mentorId = $request->input('mentor_id');
        $mentor = Mentor::find($mentorId);
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        $course = Course::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::with('chapters.lessons')
            ->with('mentor')
            ->with('images')
            ->find($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ]);
        }

        $reviews = Review::where('course_id', '=', $id)->get()->toArray();
        if (count($reviews) > 0) {
            $userIds = array_column($reviews, 'user_id');
            $users = getUserByIds($userIds);
            if ($users['status'] === 'error') {
                $reviews = [];
            } else {
                foreach ($reviews as $key => $review) {
                    $userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
                    $reviews[$key]['users'] = $users['data'][$userIndex];
                }
            }
        }

        $totalStudent = MyCourse::where('course_id', '=', $id)->count();
        $totalVideos = Chapter::where('course_id', '=', $id)->withCount('lessons')->get()->toArray();
        $finalTotalVideos = array_sum(array_column($totalVideos, 'lessons_count'));

        $course['reviews'] = $reviews;
        $course['total_videos'] = $finalTotalVideos;
        $course['total_student'] = $totalStudent;

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $rules = [
            'name' => 'string',
            'certificate' => 'boolean',
            'thumbnail' => 'string|url',
            'type' => 'string|in:free,premium',
            'status' => 'string|in:draft,published',
            'price' => 'integer',
            'lavel' => 'in:alllavel,beginer,intermediate,advance',
            'description' => 'string',
            'mentor_id' => 'integer'
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found'
            ], 404);
        }

        $mentorId = $request->input('mentor_id');
        if ($mentorId) {
            $mentor = Mentor::find($mentorId);
            if (!$mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'mentor not found'
                ], 404);
            }
        }

        $course->fill($data);
        $course->save();

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $courses = Course::find($id);
        if (!$courses) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found'
            ], 404);
        }
        $courses->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'course was delete'
        ]);
    }
}
