


class Controller extends BaseController

{ 

 	public function __construct(){



  if(!Session::get('GoogleAuth') ){
            
            $this->middleware('googleAuth');
                    
            }
            }
