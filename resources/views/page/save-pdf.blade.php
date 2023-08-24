<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style type="text/css">
    @font-face {
        font-family: 'Lato';
        font-style: normal;
        font-variant: normal;
        src: url("fonts/Lato-Regular.ttf") format('truetype');
    }
    @font-face {
        font-family: 'Lato-Bold';
        font-weight: '700';
        font-style: normal;
        font-variant: normal;
        src: url("fonts/Lato-Bold.ttf") format('truetype');
    }
    @font-face {
        font-family: 'Prata';
        font-style: normal;
        font-variant: normal;
        src: url("fonts/Prata-Regular.ttf") format('truetype');
    }
    html,  body {
        margin: 0 !important;
        padding: 0 !important;
        font-family: "Lato", sans-serif;
        font-weight: normal;
    }
    body { font-family: "Lato", sans-serif; color: #44546a; font-size: 16px; line-height: 1.5; }
    table {
        border-spacing: 0 !important;
        border-collapse: collapse !important;
        /* table-layout: fixed !important; */
        margin: 0 auto !important;
    }
    table table table {
        table-layout: auto;
    }
    .page-break {
        page-break-after: always;
    }
    .no-page-break {
        page-break-after: never;
    }
    header{
        margin-top: 0;
        margin-bottom: 0;
    }
   
    
</style>
    </head> 
    <body bgcolor="#ffffff" style="margin: 0;">
        

       
        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" align="top"  style="border-collapse:collapse;" width="100%">
            <tbody>
                
                <tr>
                    <td align="top">
                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"  align="top" style="border-collapse:collapse;" width="100%">
                            <tbody>
                            <tr>
                        <td align="top" class="pdf-header" style="background-color: #F5F7FD; padding: 20px 0;">
                        
                            <table align="top" cellpadding="0" cellspacing="0"  width="100%">
                                <tbody>
                                    <tr>
                                        <td align="center" >
                                        <a class="" href="{{ url('/home/') }}"><img   src="{{asset('images/new-logo.png')}}" alt="Wellkasa" title="Wellkasa"></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>                              
                        </td>
                    </tr>
                                <tr>
                                    <td>
                                        <table align="top" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                        <td style="padding:10px; ">
                                            @foreach($finalDurgMedicinesData as $drug)
                                                <div style="display: inline-block; color:#44546a; margin:5px; font-size: 12px; background-color: #f2f2f2; border: none; box-shadow: 0px 4px 4px rgb(0 0 0 / 10%); border-radius: 18px; padding:4px 10px; ">
                                                    <table align="top" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td>
                                                                <img style=" padding-right: 10px;" src="{{asset('images')}}/{{$drug['image']}}" alt="Wellkasa" title="pill1">
                                                            </td>
                                                            <td>
                                                                <span style="vertical-align: middle;">{{ $drug['name']}}</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            @endforeach
                                        </td>
                                        </tr>
                                        </table>
                                            @if(array_key_exists('major' , $severityCheckboxArray) && array_key_exists('moderate' , $severityCheckboxArray) && array_key_exists('minor' , $severityCheckboxArray))
                                            <table align="top" cellpadding="0" cellspacing="0"   width="100%">  
                                            <tr>
                                                <td style="padding:10px; color: #44546a; font-size: 16px; width: 200px;">  
                                                    Severity
                                                </td>  
                                                <td style="width: 100px; padding:20px 10px 10px 10px;text-align: center; color: #44546A; font-size: 12px;">  
                                                <img  src="{{ array_key_exists('major' , $severityCheckboxArray) ? asset('images').'/'.$severityCheckboxArray['major'] : ''}}" alt="red" title="red">
                                                    <p style=" color: #44546A; padding: 0; margin: 0; font-size: 12px;">Major </p>
                                                </td>
                                                <td style="width: 100px; padding:20px 10px 10px 10px;text-align: center; color: #44546A; font-size: 12px;">  
                                                <img  src="{{ array_key_exists('moderate' , $severityCheckboxArray) ? asset('images').'/'.$severityCheckboxArray['moderate'] : ''}}" alt="yellow" title="yellow">
                                                    <p style=" color: #44546A; padding: 0; margin: 0; font-size: 12px;">Moderate </p>
                                                </td>
                                                <td style="width: 100px; padding:20px 10px 10px 10px;text-align: center; color: #44546A; font-size: 12px;">  
                                                <img  src="{{ array_key_exists('minor' , $severityCheckboxArray) ? asset('images').'/'.$severityCheckboxArray['minor'] : ''}}" alt="green" title="green">
                                                    <p style=" color: #44546A; padding: 0; margin: 0; font-size: 12px;">Minor </p>
                                                </td>
                                                   
                                            </tr>
                                            
                                            </table>  
                                            @else
                                            <table align="top" cellpadding="0" cellspacing="0"   width="100%">  
                                            <tr>
                                                <td style="padding:10px; color: #44546a; font-size: 16px; width: 200px;">  
                                                    Missing Natural therapies / Drugs data to display any interactions
                                                </td>                                                   
                                            </tr>
                                            
                                            </table> 
                                            @endif 
                                    </td>    
                                </tr>  
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table align="top" cellpadding="0" cellspacing="0"   width="100%">  
            @foreach($lastArray as $key => $interactionsval)
               
                @foreach($interactionsval as $interactionsvalKey => $interactionsvalData)
                    @php
                        $imageName = 'pill.svg';
                        if($interactionsvalData[0]['therapyType'] == "Food, Herbs & Supplements"){
                            $imageName = 'pill.svg';
                        } else if($interactionsvalData[0]['therapyType'] == "Health & Wellness"){
                            $imageName = 'yoga.svg';
                        } 
                    @endphp
                @endforeach
                <tr>
                    <td style="padding:10px; padding-top:15px; font-weight: bold; color: #44546a; font-size: 16px; font-weight: bold;">  
                    <img style="vertical-align: sub; padding-right: 10px;" src="{{asset('images')}}/{{$imageName}}" alt="Wellkasa" title="drug">  {{$key}}
                    </td>    
                </tr> 
                
                @foreach($interactionsval as $key => $val)

                    <tr>
                        <td style="padding:10px 20px 10px 20px; background: #f5f5f5; color: #44546A; font-size: 12px; border-bottom: 5px solid #fff;">  
                            @foreach($val as $valKey => $valData)
                            
                                @if($valKey == '0')
                                    <img style=" padding-right: 10px;" src="{{asset('images/')}}/{{$valData['circle_class']}}" alt="Wellkasa" title="pill1">
                                    <span style="vertical-align: middle;"> {{$key}}</span>  
                                @endif

                                @if($valData['isInteractionsFound'] == 0)
                                    <span style="float:right">No interactions found</span>
                                @else
                                    <div style="padding:10px; font-size: 14px; line-height: 18px;">
                                        <span> <b>{{$valData['title']}}</b></span><br><br>
                                        <span> Interaction Rating = <span class="text-danger">{{$valData['interactionRating']}}</span></span><br><br>
                                        <span> Severity = {{$valData['severity']}}</span><br>
                                        <span> Occurrence = {{$valData['occurrence']}}</span><br>
                                        <span> Level of Evidence = {{$valData['levelOfEvidence']}}</span><br><br>
                                        <span><?php echo $valData['description'];?></span><br>
                                    </div>
                                @endif
                                
                            @endforeach
                        </td>
                    </tr>
                    
                @endforeach  
            @endforeach
        </table>   
        <footer>
            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" align="bottom"  style="border-collapse:collapse;" width="100%">
                <tbody>
                    <tr>
                        <td style="padding: 30px 0;">
                            <table align="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;"  width="100%">
                                <tr>
                                    <td width="100" style="background: #44546A;">
                                        <img height="100" src="{{asset('images/report-img.png')}}" alt="report" title="report">
                                    </td>
                                    <td  style="background-color: #44546A;  ">
                                        <img  src="{{asset('images/report-logo.png')}}" alt="report" title="report">
                                        <p style="color: #FFFFFF;padding-left:5px; font-size: 15px; margin: 0; ">Get access to science on natural<br> medicines for <a style="color: #FDCA40; text-decoration: none;" href="#"> FREE</a>. </p>
                                    </td>
                                    <td style="background: #44546A; text-align: right; padding-right: 15px; border-top-right-radius: 4px; border-bottom-right-radius: 4px;">
                                        <a style="color: #FDCA40; text-decoration: none; " href="#">
                                            <img style="padding-top:15px" src="{{asset('images/signup.svg')}}" alt="report" title="report"> 
                                        </a>
                                    </td>
                                </tr>
                            </table>  
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table align="top" cellpadding="0" cellspacing="0"   width="100%">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 20px;">    
                                        <img width="50" src="{{asset('images/mobilelogo.png')}}" alt="mobilelogo" title="mobilelogo">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </footer>
       
    </body>
</html>

