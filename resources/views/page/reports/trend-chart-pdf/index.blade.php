<html>
    <head>
        @include('page.reports.trend-chart-pdf.head')
    </head>
    <body bgcolor="#ffffff" style="margin: 0;">
        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse;" width="100%">
            <tbody>
                <tr>
                    <td  valign="top">
                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"  valign="top" style="border-collapse:collapse;" width="100%">
                            <tbody>
                                <tr>
                                    <td valign="top" class="pdf-header" style="background-color: #F5F7FD; padding: 20px 0;">
                                    <table valign="top" cellpadding="0" cellspacing="0"  width="100%">
                                        <tbody>
                                            <tr>
                                                <td style="padding-left:10px; width:130px; padding-bottom: 14px;" >
                                                    <img  src="{{asset('images/rx-img.png')}}" alt="logo"> 
                                                </td>
                                                <td  style="text-align: center; font-size: 22px; color: #44546A; padding-bottom: 14px; font-family: Prata; line-height: 22px;">
                                                    {{isset($titleHeader) ? $titleHeader : 'Wellness by Design'}} <br>
                                                    <span style="text-align: center;font-size: 18px;color: #44546A; font-family: Prata;line-height: 14px;"> {{$userFirstName}}'s {{$titleName}}</span>
                                                </td>
                                                <td style="padding-right:10px; width:100px; text-align: right; padding-bottom: 14px;" >
                                                    <a href="https://wellkasa.com" style="display:inline-block;" target="_blank">
                                                        <img  src="{{asset('images/pdf-wellkasa-logo.png')}}" alt="tour-logo"> 
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                        <table valign="top" cellpadding="0" cellspacing="0"  width="100%" style="border-top: 1px solid #F2AB57; ">
                                            <tbody>
                                                
                                                <tr>
                                                    <td>
                                                        <table valign="top" cellpadding="0" cellspacing="0"  width="100%">
                                                            <tr>
                                                                <td style="text-align: right; width:50%; padding-top: 14px; font-size: 12px; padding-right: 10px; color: #44546A;">
                                                                    Created by: {{isset($providerName) ? $providerName : $userName}}
                                                                </td>
                                                                <td style="text-align: left; width:50%; padding-top: 14px; font-size: 12px; padding-left: 10px; color: #44546A;">
                                                                Created On: {{$createdOnDate}}
                                                                </td>
                                                            </tr>
                                                        </table> 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>  
                                    </td>
                                </tr>
                                <tr  >
                                    <!-- Trend Chart Start -->
                                    <td>                                        
                                        <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse;" width="100%">
                                            <tr>
                                                <td style="font-size: 18px;  padding: 24px 22px 20px 22px; color: #44546A; text-align: center; font-weight:700;">
                                                    Trend Chart <br>{{date('m/d/y',strtotime(reset($firstLastDaysDateArray)))}} - {{date('m/d/y',strtotime(end($firstLastDaysDateArray)))}}
                                                </td>
                                            </tr>
                                            <!-- legend start  -->
                                            <tr>
                                                <td>
                                                    <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse; margin:auto;" width="45%">
                                                        <tr>
                                                            @foreach($firstSeverityNames as $severityName)
                                                                <!-- Hide none & major severity and display others from symptoms list - start -->
                                                                @if(!in_array($severityName,['Major']))
                                                                    <td style="text-align:center;">
                                                                        <!-- Display the severity color by the severity name - Start -->
                                                                        <span class="{{strtolower($severityName)}}" style="border-radius: 10px; width: 50px; height: 6px; display: block; margin: auto;"></span>
                                                                        <!-- Display the severity color by the severity name - End -->
                                                                        <!-- Display the severity severity name - Start -->
                                                                        <span style=" color: #44546A; font-weight: 700; font-size: 12px;">{{$severityName}}</span>
                                                                        <!-- Display the severity severity name - End -->
                                                                    </td>
                                                                @endif
                                                                <!-- Hide none & major severity and display others from symptoms list - end -->
                                                            @endforeach
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <!-- legend End  -->
                                            <tr>
                                                <td style="padding:50px 120px 15px 0; position: relative;">
                                                    <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse; margin:auto; width: 90%;" >
                                                        <!-- If Symptom names are available then execute below structure - End -->
                                                            @foreach($firstSymptomNames as $firstSymptomNamesKey => $data)
                                                                <tr>
                                                                    <!-- Display Symptom Name - Start -->
                                                                    <td width="100" valign="top" style="color: {{ empty($firstTrendChartData) ? '#A2A9B4' : '#44546A' }}; font-weight: 700; font-size: 12px; padding: 5px 10px 5px 10px; text-align: right;  ">
                                                                        {{$data->symptomName}}
                                                                    </td>
                                                                    <!-- Display Symptom Name - End -->

                                                                    <!-- Trend chart data is not available then execute below structure - Start -->
                                                                    @if(empty($firstTrendChartData) && $firstSymptomNamesKey == '0')
                                                                        <!-- Adjust the rowspan according to symptom count list to adjust the message view -->
                                                                        <td rowspan="{{count($firstSymptomNames)}}">
                                                                            <div style="text-align:center"><span  style="font-weight: 700;  font-size: 12px;"> {{trans('messages.graph_data_not_available')}}</span></div> 
                                                                        </td>
                                                                    @endif
                                                                    <!-- Trend chart data is not available then execute below structure - End -->
                                                                  

                                                                    <!-- If Trend chart data is available then execute below structure - Start -->
                                                                    @if(!empty($firstTrendChartData))
                                                                        <!-- Display the color code for the severity of the symptom - Start -->
                                                                        <td style="position: relative; ">
                                                                            <table style=" position: relative; z-index: 1; width:98%; ">
                                                                                <tr>
                                                                                    @foreach($firstLastDaysDateArray as $date)
                                                                                        <td  style=" padding: 10px 1px; ">
                                                                                            <!-- Fetch the severity name and use it as a color code - start -->
                                                                                            <span class="{{strtolower($firstTrendChartData[$date][$data->symptomName])}}" style="border-radius: 50px; height: 10px; width:100%; display: inline-block;"></span>    
                                                                                            <!-- Fetch the severity name and use it as a color code - start -->             
                                                                                        </td>
                                                                                    @endforeach
                                                                                </tr>
                                                                            </table>
                                                                            <div style="background: rgba(245, 245, 245, 0.6); border: 0.5px solid #D9D9D9; height: 22px;   border-radius: 10px;  position: absolute;  top: 3px;  left: 0px; right: 0; z-index: 0;  width: 100% "></div>
                                                                        </td>
                                                                        <!-- Display the color code for the severity of the symptom - End -->                                               
                                                                    @endif
                                                                    <!-- If Trend chart data is available then execute below structure - End -->
                                                                </tr>
                                                            @endforeach
                                                        <!-- If Symptom names are available then execute below structure - End -->
                                                    


                                                        <!-- Display Last N dates - start -->
                                                        <tr>
                                                            <td width="100"  style="color: #44546A; font-weight: 700; font-size: 0px; padding: 0px 10px;     text-align: right;">
                                                                &nbsp;
                                                            </td>
                                                            

                                                            <td class="{{ empty($firstTrendChartData) ? 'mt-4' : '' }}" valign="top" style="position: relative; ">
                                                           
                                                                <table width="100%" style="position: relative; z-index: 1;">
                                                                    <tr>
                                                                        @foreach($firstLastDaysDateArray as $date)
                                                                            <td valign="top" class="date-list"  style=" padding:0px 2px; text-align: center;">
                                                                                
                                                                                    <span style=" display: inline-block; color: rgba(0, 0, 0, 0.38); transform: rotate(-60deg); font-weight: 700;   font-size: 8px; ">{{date('d',strtotime($date))}}</span>    
                                                                                
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <!-- Display Last N dates - end -->
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <!-- Trend Chart End -->  

                                </tr> 
                                

                                <!--- page break start --->  
                                 <div class="page-break"></div>
                                <!--- page break end --->  

                                <!-- Second Trend Chart Data - Start -->
                                <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse;" width="100%">
                                    <tr>
                                        <td style="font-size: 18px;  padding: 24px 22px 20px 22px; color: #44546A; text-align: center; font-weight:700;">
                                            Trend Chart <br>{{date('m/d/y',strtotime(reset($secondLastDaysDateArray)))}} - {{date('m/d/y',strtotime(end($secondLastDaysDateArray)))}}
                                        </td>
                                    </tr>
                                    <!-- legend start  -->
                                    <tr>
                                        <td>
                                            <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse; margin:auto;" width="45%">
                                                <tr>
                                                    @foreach($secondSeverityNames as $severityName)
                                                        <!-- Hide none & major severity and display others from symptoms list - start -->
                                                        @if(!in_array($severityName,['Major']))
                                                            <td style="text-align:center;">
                                                                <!-- Display the severity color by the severity name - Start -->
                                                                <span class="{{strtolower($severityName)}}" style="border-radius: 10px; width: 50px; height: 6px; display: block; margin: auto;"></span>
                                                                <!-- Display the severity color by the severity name - End -->
                                                                <!-- Display the severity severity name - Start -->
                                                                <span style=" color: #44546A; font-weight: 700; font-size: 12px;">{{$severityName}}</span>
                                                                <!-- Display the severity severity name - End -->
                                                            </td>
                                                        @endif
                                                        <!-- Hide none & major severity and display others from symptoms list - end -->
                                                    @endforeach
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <!-- legend End  -->
                                    <tr>
                                        <td style="padding:50px 120px 15px 0; position: relative;">
                                            <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse; margin:auto; width: 90%;" >
                                                <!-- If Symptom names are available then execute below structure - End -->
                                                    @foreach($secondSymptomNames as $secondSymptomNamesKey => $data)
                                                        <tr>
                                                            <!-- Display Symptom Name - Start -->
                                                            <td width="100" valign="top" style="color: {{ empty($secondTrendChartData) ? '#A2A9B4' : '#44546A' }}; font-weight: 700; font-size: 12px; padding: 5px 10px 5px 10px; text-align: right;  ">
                                                                {{$data->symptomName}}
                                                            </td>
                                                            <!-- Display Symptom Name - End -->

                                                            <!-- Trend chart data is not available then execute below structure - Start -->
                                                            @if(empty($secondTrendChartData) && $secondSymptomNamesKey == '0')
                                                                <!-- Adjust the rowspan according to symptom count list to adjust the message view -->
                                                                <td rowspan="{{count($secondSymptomNames)}}">
                                                                    <div style="text-align:center"><span  style="font-weight: 700;  font-size: 12px;"> {{trans('messages.graph_data_not_available')}}</span></div> 
                                                                </td>
                                                            @endif
                                                            <!-- Trend chart data is not available then execute below structure - End -->
                                                            
                                                            <!-- If Trend chart data is available then execute below structure - Start -->
                                                            @if(!empty($secondTrendChartData))
                                                                <!-- Display the color code for the severity of the symptom - Start -->
                                                                <td style="position: relative; ">
                                                                    <table style=" position: relative; z-index: 1; width:98%; ">
                                                                        <tr>
                                                                            @foreach($secondLastDaysDateArray as $date)
                                                                                <td  style=" padding: 10px 1px; ">
                                                                                    <!-- Fetch the severity name and use it as a color code - start -->
                                                                                    <span class="{{strtolower($secondTrendChartData[$date][$data->symptomName])}}" style="border-radius: 50px; height: 10px; width:100%; display: inline-block;"></span>    
                                                                                    <!-- Fetch the severity name and use it as a color code - start -->             
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    </table>
                                                                    <div style="background: rgba(245, 245, 245, 0.6); border: 0.5px solid #D9D9D9; height: 22px;   border-radius: 10px;  position: absolute;  top: 3px;  left: 0px; right: 0; z-index: 0;  width: 100% "></div>
                                                                </td>
                                                                <!-- Display the color code for the severity of the symptom - End -->                                               
                                                            @endif
                                                            <!-- If Trend chart data is available then execute below structure - End -->
                                                        </tr>
                                                    @endforeach
                                                <!-- If Symptom names are available then execute below structure - End -->
                                            


                                                <!-- Display Last N dates - start -->
                                                <tr>
                                                    <td width="100"  style="color: #44546A; font-weight: 700; font-size: 0px; padding: 0px 10px;     text-align: right;">
                                                        &nbsp;
                                                    </td>
                                                    

                                                    <td class="{{ empty($secondTrendChartData) ? 'mt-4' : '' }}"  valign="top" style="position: relative; ">
                                                    
                                                        <table width="100%" style="position: relative; z-index: 1;">
                                                            <tr>
                                                                @foreach($secondLastDaysDateArray as $date)
                                                                    <td valign="top" class="date-list"  style=" padding:0px 2px; text-align: center;">
                                                                        
                                                                        <span style=" display: inline-block; color: rgba(0, 0, 0, 0.38); transform: rotate(-60deg); font-weight: 700;   font-size: 8px; ">{{date('d',strtotime($date))}}</span>    
                                                                        
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- Display Last N dates - end -->
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!-- Second Trend Chart Data - End -->


                                <!--- page break start --->  
                                  <div class="page-break"></div>
                                <!--- page break end --->  

                                <!-- Third Trend Chart Data - Start -->
                                <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse;" width="100%">
                                    <tr>
                                        <td style="font-size: 18px;  padding: 24px 22px 20px 22px; color: #44546A; text-align: center; font-weight:700;">
                                            Trend Chart <br>{{date('m/d/y',strtotime(reset($thirdLastDaysDateArray)))}} - {{date('m/d/y',strtotime(end($thirdLastDaysDateArray)))}}
                                        </td>
                                    </tr>
                                    <!-- legend start  -->
                                    <tr>
                                        <td>
                                            <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse; margin:auto;" width="45%">
                                                <tr>
                                                    @foreach($thirdSeverityNames as $severityName)
                                                        <!-- Hide none & major severity and display others from symptoms list - start -->
                                                        @if(!in_array($severityName,['Major']))
                                                            <td style="text-align:center;">
                                                                <!-- Display the severity color by the severity name - Start -->
                                                                <span class="{{strtolower($severityName)}}" style="border-radius: 10px; width: 50px; height: 6px; display: block; margin: auto;"></span>
                                                                <!-- Display the severity color by the severity name - End -->
                                                                <!-- Display the severity severity name - Start -->
                                                                <span style=" color: #44546A; font-weight: 700; font-size: 12px;">{{$severityName}}</span>
                                                                <!-- Display the severity severity name - End -->
                                                            </td>
                                                        @endif
                                                        <!-- Hide none & major severity and display others from symptoms list - end -->
                                                    @endforeach
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <!-- legend End  -->
                                    <tr>
                                        <td style="padding:50px 120px 15px 0; position: relative;">
                                            <table border="0" cellpadding="0" cellspacing="0" valign="top"  style="border-collapse:collapse; margin:auto; width: 90%;" >
                                                <!-- If Symptom names are available then execute below structure - End -->
                                                    @foreach($thirdSymptomNames as $thirdSymptomNameKey => $data)
                                                        <tr>
                                                            <!-- Display Symptom Name - Start -->
                                                            <td width="100" valign="top" style="color: {{ empty($thirdTrendChartData) ? '#A2A9B4' : '#44546A' }}; font-weight: 700; font-size: 12px; padding: 5px 10px 5px 10px; text-align: right;  ">
                                                                {{$data->symptomName}}
                                                            </td>
                                                            <!-- Display Symptom Name - End -->
                                                           
                                                            <!-- Trend chart data is not available then execute below structure - Start -->
                                                            @if(empty($thirdTrendChartData) && $thirdSymptomNameKey == '0')
                                                                <!-- Adjust the rowspan according to symptom count list to adjust the message view -->
                                                                <td rowspan="{{count($thirdSymptomNames)}}">
                                                                    <div style="text-align:center"><span  style="font-weight: 700;  font-size: 12px;"> {{trans('messages.graph_data_not_available')}}</span></div> 
                                                                </td>
                                                            @endif
                                                            <!-- Trend chart data is not available then execute below structure - End -->

                                                            <!-- If Trend chart data is available then execute below structure - Start -->
                                                            @if(!empty($thirdTrendChartData))
                                                                <!-- Display the color code for the severity of the symptom - Start -->
                                                                <td style="position: relative; ">
                                                                    <table style=" position: relative; z-index: 1; width:98%; ">
                                                                        <tr>
                                                                            @foreach($thirdLastDaysDateArray as $date)
                                                                                <td  style=" padding: 10px 1px; ">
                                                                                    <!-- Fetch the severity name and use it as a color code - start -->
                                                                                    <span class="{{strtolower($thirdTrendChartData[$date][$data->symptomName])}}" style="border-radius: 50px; height: 10px; width:100%; display: inline-block;"></span>    
                                                                                    <!-- Fetch the severity name and use it as a color code - start -->             
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    </table>
                                                                    <div style="background: rgba(245, 245, 245, 0.6); border: 0.5px solid #D9D9D9; height: 22px;   border-radius: 10px;  position: absolute;  top: 3px;  left: 0px; right: 0; z-index: 0;  width: 100% "></div>
                                                                </td>
                                                                <!-- Display the color code for the severity of the symptom - End -->                                               
                                                            @endif
                                                            <!-- If Trend chart data is available then execute below structure - End -->
                                                        </tr>
                                                    @endforeach
                                                <!-- If Symptom names are available then execute below structure - End -->
                                            


                                                <!-- Display Last N dates - start -->
                                                <tr>
                                                    <td width="100"   style="color: #44546A; font-weight: 700; font-size: 0px; padding: 0px 10px;     text-align: right;">
                                                        &nbsp;
                                                    </td>                     

                                                    <td class="{{ empty($thirdTrendChartData) ? 'mt-4' : '' }}" valign="top" style="position: relative; ">
                                                    
                                                        <table width="100%" style="position: relative; z-index: 1;">
                                                            <tr>
                                                                @foreach($thirdLastDaysDateArray as $date)
                                                                    <td valign="top" class="date-list"  style=" padding:0px 2px; text-align: center;">
                                                                        
                                                                        <span style=" display: inline-block; color: rgba(0, 0, 0, 0.38); transform: rotate(-60deg); font-weight: 700;   font-size: 8px; ">{{date('d',strtotime($date))}}</span>    
                                                                        
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- Display Last N dates - end -->
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!-- Third Trend Chart Data - Start --> 

                             
                            </tbody>
                        </table>
                    </td>
                    
                </tr>

                
                
            </tbody>
        </table>

        <footer>
            <!---- Footer start --->
            @include('page.reports.trend-chart-pdf.footer')
            <!---- Footer end --->
        </footer>

    </body>
</html>