<html>
    <head>
        @include('page.reports.integrative-report.head')
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
                                                <tr>
                                                    <td  style="text-align: center; font-size: 24px; color: #44546A; padding-bottom: 14px; font-family: Prata;">
                                                    Integrative Protocol
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table align="top" cellpadding="0" cellspacing="0"  width="100%">
                                                            <tr>
                                                                <td style="text-align: right; font-size: 12px; padding-right: 10px; color: #44546A;">
                                                                    Created by: {{$finalArray['createdBy']}}
                                                                </td>
                                                                <td style="text-align: left; font-size: 12px; padding-left: 10px; color: #44546A;">
                                                                Created On:  {{$finalArray['createdOn']}}
                                                                </td>
                                                            </tr>
                                                        </table> 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>  
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                            // Get array keys
                                            $arrayKeys = array_keys($finalArray['therapyData']);
                                            // Fetch last array key
                                            $lastArrayKey = array_pop($arrayKeys);
                                        ?>
                                        @foreach($finalArray['therapyData'] as $key => $value)
                                            <table align="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;"  width="100%">
                                                <tr>
                                                    <td style="padding-bottom:5px; padding-top:20px; font-weight: bold; color: #44546A; font-size: 16px;">  
                                                        <img style="vertical-align: text-top; padding-right:5px;" src="{{$value[0]['therapyIcon']}}" alt="Wellkasa" title="pill1"> {{$key}}
                                                    </td>    
                                                </tr>                                     
                                                <tr>
                                                    <td style="padding-bottom:10px;  color: #44546A; font-size: 12px; padding-left:27px;">  
                                                    To view benefits of {{$value[0]['therapyCanonicalName']}}  <a style="color:#35C0ED; text-decoration: none;" target="_blank" href="{{$value[0]['therapyRoute']}}">click here</a>
                                                    </td>    
                                                </tr>
                                                <tr>
                                                    <td style="background: #7380B4; height: 1px; line-height: 1px;">  
                                                        &nbsp;
                                                    </td>    
                                                </tr>
                                                <tr>
                                                    <td style="background: #fff; height: 5px; line-height: 5px;">  
                                                        &nbsp;
                                                    </td>    
                                                </tr>  
                                            </table>

                                            <table align="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;" width="100%">
                                                
                                                @foreach($value as $valueKey => $valueData)
                                                <tr>
                                                    
                                                    <td style="background: #FAFAFA; padding: 12px 25px; border-radius: 4px;  border-bottom: 10px solid #fff;">
                                                        
                                                        <div style="font-weight: bold; font-size: 12px; color: #44546A;">
                                                            {{$valueData['conditionName']}}
                                                        </div>
                                                        <p style="font-size: 12px; line-height: 16px; color: #44546A;">
                                                            {!! html_entity_decode(nl2br($valueData['conditionNameDetails'])) !!}
                                                        </p>
                                                            
                                                    </td>
                                                    
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td style="font-size: 12px; line-height: 16px; color: #44546A;">
                                                        To add {{$value[0]['therapyCanonicalName']}} to your therapy list and journal your observations <a style="color:#35C0ED; text-decoration: none;" href="{{route('login')}}">sign up at Wellkasa  here</a>
                                                    </td>
                                                </tr>
                                                
                                            </table>
                                            <!--- page break till new therapy code start --->  
                                            @if($key != $lastArrayKey)
                                                <div class="page-break"></div>
                                            @endif
                                            <!--- page break till new therapy code end --->  
                                        @endforeach

                                    </td>    
                                </tr>  
                                
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <footer>
            <!---- Footer start --->
            @include('page.reports.integrative-report.footer')
            <!---- Footer end --->
        </footer>

        <!---- Join Wellkasa Page Template start --->
        @include('page.reports.join')
        <!---- Join Wellkasa Page Template end --->

    </body>
</html>