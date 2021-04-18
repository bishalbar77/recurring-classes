<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes;
use App\User;
use Carbon\Carbon;
use App\ClassesStudents;
use Auth;
use DB;
class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Auth::user()->type=='Teacher') {
            $classStudents = ClassesStudents::all();
            $class = Classes::all();
            return view('class.index')->with(['class' => $class,
                                                'classStudents' => $classStudents
                                                ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->type=='Teacher') {
            $students = User::where('type','Student')->get();
            return view('class.create')->with(['students' => $students]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->type=='Teacher') {
        // dd($request->all());
            $students = $request->students;
            $startDate = Carbon::createFromFormat('m/d/Y g:i A', $request->startDate)->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('m/d/Y g:i A', $request->endEnd)->format('Y-m-d H:i:s');
            $classCheck = Classes::all();
            $condition = 0;
            foreach($classCheck as $check) {
                if((strftime("%H:%M",strtotime($check->startDate)) < strftime("%H:%M",strtotime($startDate))) || (strftime("%H:%M",strtotime($check->endDate)) > strftime("%H:%M",strtotime($endDate)))) {
                    if((strftime("%a",strtotime($check->startDate)) == strftime("%a",strtotime($startDate))) || (strftime("%a",strtotime($check->endDate)) == strftime("%a",strtotime($endDate)))) {
                        $studentLists = ClassesStudents::where('class_id',$check->id)->get();
                        foreach($studentLists as $studentList) {
                            foreach($students as $student) {
                                if($studentList->student_id == $student) {
                                    $condition = 1;
                                    $error = $studentList->student->name . ' already has a class on ' . strftime("%H:%M",strtotime($startDate));
                                }
                            }
                        }
                    }
                }
            }
            if($condition==1) {
                $students = User::where('type','Student')->get();
                return view('class.create')->with(['students' => $students,
                                                    'error' => $error]);
            }
            $class = new Classes();
            $class->subject = $request->subject;
            $class->startDate = $startDate;
            $class->endDate = $endDate;
            $class->teacher_id = Auth::user()->id;
            $class->save();
            foreach($students as $student) {
                $classStu = new ClassesStudents();
                $classStu->student_id = $student;
                $classStu->class_id = $class->id;
                $classStu->save();
            }

            return redirect()->route('class.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function classes()
    {
        if(Auth::user()->type=='Student') {
            $classList = ClassesStudents::where('student_id','=', Auth::user()->id)->get();
            return view('class.classes')->with(['classList' => $classList]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
