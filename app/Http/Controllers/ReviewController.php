<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
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
            'user_id' => 'required|integer',
            'course_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'note' => 'required|string'
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
            ]);
        }

        $userId = $request->input('user_id');
        $user = getUser($userId);
        if ($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['http_code']);
        }

        $isExistReview = Review::where('course_id', '=', $courseId)->where('user_id', '=', $userId)->exists();
        if ($isExistReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'review already exsist'
            ], 409);
        }

        $review = Review::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $review
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
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'rating' => 'integer|min:1|max:5',
            'note' => 'string'
        ];

        $data = $request->except('user_id', 'course_id');

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }


        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'review not found'
            ], 404);
        }

        // jika menggunakan data course dan user aktifkan ini

        // $courseId = $request->input('course_id');
        // if ($courseId) {
        //     $course = Course::find($courseId);
        //     if (!$course) {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => 'course not found'
        //         ], 404);
        //     }
        // }

        // $userId = $request->input('user_id');
        // if ($userId) {
        //     $user = getUser($userId);
        //     if ($user['status'] === 'error') {
        //         return response()->json([
        //             'status' => $user['status'],
        //             'message' => $user['message']
        //         ], $user['http_code']);
        //     }
        // }

        // $isExistReview = Review::where('course_id', '=', $courseId)->where('user_id', '=', $userId)->exists();
        // if ($isExistReview) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'review not found'
        //     ], 409);
        // }

        $review->fill($data);
        $review->save();
        return response()->json([
            'status' => 'success',
            'data' => $review
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Review::find($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found'
            ], 404);
        }

        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'rewiew was delete'
        ]);
    }
}
