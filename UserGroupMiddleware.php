<?php

namespace App\Http\Middleware;

use Closure;
use App\PHPGangsta_GoogleAuthenticator;
use Session;
use App\Http\Requests;
//use App\Http\Request;
use App\User;
use Auth; 
use Illuminate\Http\Response;
use Illuminate\Http\Request;
class UserGroupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { 
            
        //var_dump($_REQUEST['secret']);
        //User::where('id', '=', Auth::user()->id)->update(['auth_secret' =>'']);
         $lang = $request->segment(1);
         $user = User::where('id', '=', Auth::user()->id)->first();
         if( $request->session()->get('GoogleAuth') || !$user->step_auth )
         {
            return $next($request);
         }   
            $website = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER["HTTP_HOST"].'/'.$lang; //Your Website  
         if( $request->session()->get('secret') && isset($_GET['otp'] ))
         {
             
            $authenticator = new PHPGangsta_GoogleAuthenticator(); 
            $tolerance = 0; 
            $checkResult = $authenticator->verifyCode($request->session()->get('secret'), $_REQUEST["otp"] , $tolerance);    
            if ($checkResult) 
            {
                $request->session()->put('GoogleAuth', '1');
                User::where('id', '=', Auth::user()->id)->update(['auth_secret' =>$request->session()->get('secret') ]);
                return $next($request);
            }else{
                Auth::logout();
                $data["expire"] = 1;
                return new Response(view('auth.step_auth')->with($data)->with('website',$website)->with('your_note_count', 0)->with('logo_file', '')->with('tutor_globalflag',  0)); 
            }
        }
  
                $title= 'Jumpnotes';
           // if(empty($user->auth_secret)){                
                $authenticator = new PHPGangsta_GoogleAuthenticator();
                if(empty($user->auth_secret)){
                $secret = $authenticator->createSecret();
                $request->session()->put( 'secret',  $secret );
                $qrCodeUrl = $authenticator->getQRCodeGoogleUrl($title, $secret,$website);
                $data["url"] =  $qrCodeUrl;
                }else{
                    $secret = $user->auth_secret;
                    $request->session()->put( 'secret',  $secret );
                    $data["url"] = null;
                }               
                
                //$data["fullUrl"] = $fullUrl;
                $data["secret"] = $secret;
                $data["website"] = $website;               
                
                return new Response(view('auth.step_auth')->with($data)->with('your_note_count', 0)->with('logo_file', '')->with('tutor_globalflag',  0));          

         //var_dump($request->session()->get('secret'));
         
        //$request->segment(1);
       // var_dump($user->auth_secret);
            
        
    }

    function step_authentication(Request $request){
        $fullUrl = null;//urlencode($request->fullUrl());

                   
    }
    
    
     function oauth2callback($secret , $otp ){
           

              
    }
}
