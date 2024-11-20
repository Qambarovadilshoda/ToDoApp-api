<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('user', 'category')->paginate(10);
        return response()->json([
            'tasks' => TaskResource::collection($tasks),
            'links' => [
                'first' => $tasks->url(1),
                'last' => $tasks->url($tasks->lastPage()),
                'prev' => $tasks->previousPageUrl(),
                'next' => $tasks->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'from' => $tasks->firstItem(),
                'last_page' => $tasks->lastPage(),
                'path' => $tasks->path(),
                'per_page' => $tasks->perPage(),
                'to' => $tasks->lastItem(),
                'total' => $tasks->total(),
            ],
        ]);
    }
    public function store(StoreTaskRequest $request)
    {
        $term = Carbon::parse($request->term);
        $time = Carbon::createFromFormat('H:i', $request->time, 'Asia/Tashkent');
        $task = new Task();
        $task->user_id = Auth::id();
        $task->category_id = $request->category_id;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = 'Not Done';
        $task->term = $term;
        $task->time = $time->format('H:i');

        $task->save();
        return response()->json([
            'task' => new TaskResource($task->load('category')),
        ], 201);
    }
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json([
            'task' => new TaskResource($task),
        ]);
    }
    public function update(UpdateTaskRequest $request, $id)
    {
        $term = Carbon::parse($request->term);
        $time = Carbon::createFromFormat('H:i', $request->time, 'Asia/Tashkent');

        $task = Task::findOrFail($id);
        if (Auth::id() !== $task->user_id) {
            return response()->json([
                'message' => "This task isn't yours",
            ], 403);
        }
        $task->category_id = $request->category_id;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->term = $term;
        $task->time = $time->format('H:i');
        $task->save();
        return response()->json([
            'message' => 'Task updated',
            'task' => new TaskResource($task->load('category')),
        ]);
    }
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        if (Auth::id() !== $task->user_id) {
            return response()->json([
                'message' => "This task isn't yours",
            ], 403);
        }
        $task->delete();
        return response()->json([
            'message' => 'Task deleted',
        ], 204);
    }
    public function search(Request $request)
    {
        $tasks = Task::when("%$request->q%", function ($query, $q) {
            return $query->where('title', 'like', "$q");
        })->paginate(8);

        return response()->json([
            'tasks' => TaskResource::collection($tasks->load('category')),
            'links' => [
                'first' => $tasks->url(1),
                'last' => $tasks->url($tasks->lastPage()),
                'prev' => $tasks->previousPageUrl(),
                'next' => $tasks->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'from' => $tasks->firstItem(),
                'last_page' => $tasks->lastPage(),
                'path' => $tasks->path(),
                'per_page' => $tasks->perPage(),
                'to' => $tasks->lastItem(),
                'total' => $tasks->total(),
            ],
        ]);
    }
    public function markDone($id)
    {
        $task = Task::findOrFail($id);
        $task->status = 'Done';
        $task->save();
        return response()->json([
            'message' => 'The task was marked as completed'
        ]);
    }
}
