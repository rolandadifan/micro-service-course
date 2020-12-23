<?php

namespace App\Http\Controllers;

use App\Models\ImageCourse;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $rules = [
            'course_id' => 'required|integer',
            'image' => 'required|url'
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $courseId = $request->input('course_id');
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $imgcourse = ImageCourse::create($data);
        return response()->json([
            'status' => 'oke',
            'data' => $imgcourse
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
     * @param  \App\Models\ImageCourse  $imageCourse
     * @return \Illuminate\Http\Response
     */
    public function show(ImageCourse $imageCourse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ImageCourse  $imageCourse
     * @return \Illuminate\Http\Response
     */
    public function edit(ImageCourse $imageCourse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImageCourse  $imageCourse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'course_id' => 'integer',
            'image' => 'url'
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $imgcourse = ImageCourse::find($id);
        if (!$imgcourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'image not found'
            ], 404);
        }

        $courseId = $request->input('course_id');
        if ($courseId) {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'course not found'
                ], 404);
            }
        }

        $imgcourse->fill($data);
        $imgcourse->save();
        return response()->json([
            'status' => 'oke',
            'data' => $imgcourse
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImageCourse  $imageCourse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $imgcourse = ImageCourse::find($id);
        if (!$imgcourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'image not found'
            ], 404);
        }
        $imgcourse->delete();
        return response()->json([
            'status' => 'oke',
            'data' => 'data was delete'
        ]);
    }
}
