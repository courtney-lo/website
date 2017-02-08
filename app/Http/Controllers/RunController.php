<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Run;

class RunController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	// Create a run. 
	public function postCreate(Request $request)
	{
		$email = $request->input('email');
		$name = $request->input('name');

		$run = Run::create( [
			'email'=>$email,
			'name'=>$name,
			'status'=> 'uploading',
			'dir'=>time()
		]);

		$dir = $run->directory();
		makeDir( $dir, 0775 );
		makeDir( $dir."/workingDir", 0775 );
		makeDir( $dir."/workingDir/Data", 0775 );
		makeDir( $dir."/workingDir/Library", 0775 );

		File::copy(app()->basePath().'/storage/exampleFiles/configuration.yaml', "$dir/workingDir/configuration.yaml");
		File::copy(app()->basePath().'/storage/exampleFiles/GeCKOv2_library.tsv', "$dir/workingDir/Library/GeCKOv2_library.tsv");
		
		\Illuminate\Support\Facades\Mail::to($run->email)->send(new \App\Mail\RunCreated($run));
		
		return redirect("/upload/$run->id");
	}

	// Prevent lock recipe from further uploads, start the run. 
	public function postStart($id)
	{
		$run = Run::findOrFail($id);
		$run->status='running';
		$run->save();
		$dir = $run->directory();
		File::put("$dir/status.log", 'Started');
		$runCmd = "bash ".app()->basePath()."/app/Scripts/startRun.sh $dir &";
		$runStatus = shell_exec($runCmd);
		File::put("$dir/runCmd.sh", $runCmd);
		dispatch(new \App\Jobs\MonitorRun($run));
		return "ok";
	}

	public function getResults($id)
	{
		$run = Run::findOrFail($id);
		return view('results')->with('run',$run);
	}

	public function getRuns()
	{
		return Run::all();
	}
	//
}
