<html>
    <head>
        @include('page.reports.wellkabinet-pdf.head')
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
                                                    Wellness by Design <br>
                                                    <span style="text-align: center;font-size: 18px;color: #44546A; font-family: Prata;line-height: 14px;"> {{$userFirstName}}'s Wellkabinet&#8482;</span>
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
                                                                    Created by: {{$userName}}
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
                                    <td>
                                        <!------ Interactions Icon Details - Start ------>
                                        <table valign="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;"  width="100%">
                                            <tr>
                                                <td style="padding: 20px 0;">
                                                    <table width="100%">
                                                        <tr>
                                                            <td  style="font-size: 18px;   color: #44546A; padding-bottom: 15px;">Interactions </td>
                                                        </tr>
                                                        <tr> 
                                                            <td>
                                                                <img style="vertical-align: middle; margin-bottom: -20px; " src="{{asset('images/red-info.png')}}" alt="red-info"> 
                                                                <span style="font-size: 14px; padding-left:1px; font-weight: bold;  color: #35C0ED;"> Major </span>
                                                                <div style="font-size: 12px; padding-top:3px; color: #44546A; padding-left: 45px;">  Do not take</div>
                                                            </td>
                                                            <td>
                                                                <img style="vertical-align: middle; margin-bottom: -20px;" src="{{asset('images/orange-info.png')}}" alt="orange-info"> 
                                                                <span style="font-size: 14px; padding-left:1px; font-weight: bold;  color: #35C0ED;"> Moderate </span>
                                                                <div style="font-size: 12px; padding-top:3px; color: #44546A; padding-left: 45px;">  Be cautious</div>
                                                            </td>
                                                            <td>
                                                                <img style="vertical-align: middle; margin-bottom: -20px;" src="{{asset('images/green-info.png')}}" alt="green-info"> 
                                                                <span style="font-size: 14px; padding-left:1px; font-weight: bold;  color: #35C0ED;"> Minor </span>
                                                                <div style="font-size: 12px; padding-top:3px; color: #44546A; padding-left: 45px;">  Be watchful</div>
                                                            </td>
                                                            <td>
                                                                <img style="vertical-align: middle; margin-bottom: -20px;" src="{{asset('images/gray-info.png')}}" alt="gray-info"> 
                                                                <span style="font-size: 14px; padding-left:1px; font-weight: bold;  color: #35C0ED;"> None </span>
                                                                <div style="font-size: 12px; padding-top:3px; color: #44546A; padding-left: 45px;">  No interactions</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!------ Interactions Icon Details - End ------>

                                        <!------ Medicine Types Icon Details - Start ------>
                                        <table valign="top" cellpadding="0" cellspacing="0" style="padding: 0 50px 50px 50px;"  width="100%">
                                            <tr>
                                                <td style="padding: 0 0px;">
                                                    <table width="100%">
                                                        <tr>
                                                            <td  style="font-size: 18px;   color: #44546A; padding-bottom: 15px;">Medicine Types </td>
                                                        </tr>
                                                        <tr> 
                                                            <td width="200px">
                                                                <img style="vertical-align: middle; margin-bottom: -20px; " src="{{asset('images/beta-carotene.svg')}}" alt="supplements"> 
                                                                <span style="font-size: 14px; padding-left:3px; font-weight: bold;  color: #35C0ED; margin-left: -8px;"> Supplements </span>
                                                               
                                                            </td>
                                                            <td width="145px">
                                                                <img style="vertical-align: middle; margin-bottom: -20px;" src="{{asset('images/rx-drug.svg')}}" alt="rx"> 
                                                                <span style="font-size: 14px; padding-left:3px; font-weight: bold;  color: #35C0ED; margin-left: -8px;"> Rx </span>
                                                               
                                                            </td>
                                                            <td>
                                                                <img style="vertical-align: middle; margin-bottom: -20px;" src="{{asset('images/supplement-product.png')}}" alt="sp"> 
                                                                <span style="font-size: 14px; padding-left:3px; font-weight: bold;  color: #35C0ED; margin-left: 1px;"> Supplement Product </span>
                                                               
                                                            </td>
                                                           
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!------ Medicine Types Icon Details - End ------>

                                        <!------ Wellkabinet data listing - start ------>
                                        <table valign="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;" width="100%">
                                            <tr>
                                                <td valign="top" style="padding-right:20px"  >
                                                    <table valign="top" cellpadding="0" cellspacing="0"  width="100%">
                                                        <tr>
                                                            <td valign="top">
                                                            <!-- Wellkabinet data loop - start -->
                                                                @php  $medicineCabinetDataCount = 0 @endphp
                                                                @foreach ($medicineCabinetData as $data)
                                                                    <table  style="border-bottom: 10px solid #fff;  background: #FAFAFA; width: 100%;">
                                                                        <tr>
                                                                            <td style="padding:10px; vertical-align: top;">
                                                                                @if(!empty($data['productId']))
                                                                                    @if(!empty($data['imageRedirectLink']))
                                                                                    <a href="{{$data['imageRedirectLink']}}" style="display:inline-block;" target="_blank">
                                                                                        <img width="40" height="40" src="{{$data['imageName']}}" alt="{{$data['name']}}">
                                                                                    </a>
                                                                                    @else
                                                                                        <img width="40" height="40" src="{{$data['imageName']}}" alt="{{$data['name']}}">
                                                                                    @endif
                                                                                @else
                                                                                    <img width="40" height="40" src="{{$data['imageName']}}" alt="{{$data['name']}}">
                                                                                @endif
                                                                            </td> 
                                                                            <td valign="middle" style="padding:10px;">
                                                                                <div style="width: 320px;">
                                                                                    @php $colorCode = $data['type'] == 'naturalMedicine' ? '#35C0ED' : '#44546a' @endphp
                                                                                    <div style="font-weight: bold; font-size: 14px; color: {{$colorCode}}; line-height: 16px; padding-bottom: 0px;">{{$data['name']}}@if(isset($data['canonicalName']))<a href="{{route('therapy',$data['canonicalName'])}}" target="_blank"><img width="12" height="12" style="margin-top: -2px; margin-left: 1px;" src="{{asset('images/info-blue.png')}}" alt="beta carotene"></a>@endif
                                                                                    </div>
                                                                                    @if(isset($data['conditionIds']))
                                                                                        @php $lastArrayKey = count($data['conditionIds']) @endphp
                                                                                        @foreach($data['conditionIds'] as $conditionNameKey => $conditionName)

                                                                                            @if($data['type'] != 'rxDrugs')
                                
                                                                                                <!----- Natural Medicine section condition tags - start ------->
                                                                                                @if(empty($conditionName['canonicalName']))
                                                                                                    <!-- If canonical name is not available then show condition name in unlinkable form - start -->
                                                                                                    <span style="color: #828282 !important;cursor: default; line-height:-10px; font-weight: normal; font-size: 12px; vertical-align: middle;"> 
                                                                                                        {{$conditionName['name']}} 
                                                                                                        <!-- add comma if more than one condition in list -->
                                                                                                        @if($lastArrayKey != $conditionNameKey + 1) , @endif
                                                                                                    </span>
                                                                                                    <!-- If canonical name is not available then show condition name in unlinkable form - end -->
                                                                                                @else
                                                                                                    <!-- Display conditions with redirect link if its under therapy section - start -->
                                                                                                    <a href="{{route('condition',$conditionName['canonicalName'])}}" style="color: #35C0ED; line-height:-10px; font-weight: normal; font-size: 12px; vertical-align: middle;" title="Click here to view {{$conditionName['name']}} condition efficacy chart"> 
                                                                                                        {{$conditionName['name']}} 
                                                                                                        <!-- add comma if more than one condition in list -->
                                                                                                        @if($lastArrayKey != $conditionNameKey + 1) , @endif 
                                                                                                    </a>
                                                                                                    <!-- Display conditions with redirect link if its under therapy section - end -->
                                                                                                @endif
                                                                                                <!----- Natural Medicine section condition tags - end ------->

                                                                                            @else
                                                                                                <!----- Rx Drugs & Products section condition tags - start ------->
                                                                                                <!-- Display conditions without redirect link if its under Rx Drugs & Products section - start -->
                                                                                                <span style="color: #828282 !important;cursor: default; line-height:-10px; font-weight: normal; font-size: 12px; vertical-align: middle;"> 
                                                                                                    {{$conditionName['name']}} 
                                                                                                    <!-- add comma if more than one condition in list -->
                                                                                                    @if($lastArrayKey != $conditionNameKey + 1) , @endif
                                                                                                </span>
                                                                                                <!-- Display conditions without redirect link if its under Rx Drugs & Products section - end -->
                                                                                                <!----- Rx Drugs & Products section condition tags - end ------->
                                                                                            @endif

                                                                                        @endforeach
                                                                                    @endif
                                                                                    
                                                                                </div>
                                                                            </td>  
                                                                            <!------------- Show dosage value if there, else show empty space to adjust the width - code start ---------------->
                                                                            @if(!empty($data['dosage']))
                                                                                <td style="padding:17px 10px 10px 10px; vertical-align: top; text-align:center; width: 30%;">
                                                                                    <span style="font-weight: normal; font-size: 12px; color: #44546A; display: block; line-height: 4px;"><?php echo html_entity_decode($data['dosage']); ?></span>
                                                                                </td>
                                                                            @else
                                                                                <td style="padding:10px 10px 10px 10px; vertical-align: top; text-align:center; width: 30%;">
                                                                                    <span style="font-weight: normal; font-size: 12px; color: #44546A; display: block; line-height: 10px;">No dosage<br> added</span>
                                                                                </td>
                                                                            @endif
                                                                            <!------------- Show dosage value if there, else show empty space to adjust the width - code end ---------------->

                                                                            <!------------- Show notes label if available, else show empty space to adjust the width - code start ---------------->
                                                                            @if(!empty($data['hasNotes']) && $data['hasNotes']!=0)
                                                                                <td style="padding:17px 10px 10px 10px; vertical-align: top; text-align:center;">
                                                                                    <span style="font-weight: normal; font-size: 12px; color: #44546A; display: block; line-height: 4px;"><a href="#note{{$data['medicineCabinetId']}}" style="color: #35C0ED;">Notes</a></span>
                                                                                </td>
                                                                            @else
                                                                                <td style="padding:10px 10px 10px 10px; vertical-align: top; text-align:center;">
                                                                                    <span style="font-weight: normal; font-size: 12px; color: #FAFAFA; display: block; line-height: 4px;">Empty</span>
                                                                                </td>
                                                                            @endif
                                                                            <!------------- Show notes label if available, else show empty space to adjust the width - code end ---------------->

                                                                            <td style="padding:10px; vertical-align: top;">
                                                                                @if($data['interactionLabel'] != 'noneInteraction')
                                                                                    <img width="26" height="26" style="vertical-align: top;" src="{{$data['interactionIcon']}}" alt="{{$data['interactionLabel']}}">
                                                                                @else
                                                                                    <div style="width:26px; height:26px;"></div>
                                                                                @endif  
                                                                            </td> 
                                                                            <td style="padding:10px 10px 10px 10px; vertical-align: top; text-align:center;">
                                                                                <img style="vertical-align: top;" src="{{$data['isTaking'] == '1' ? asset('images/toggle-on.png') : asset('images/toggle-off.png')}}" alt="beta carotene"> 
                                                                                <span style="font-weight: normal; font-size: 12px; color: #44546A; display: block; line-height: 4px;">Taking</span>
                                                                            </td>
                                                                            
                                                                        
                                                                            
                                                                        </tr>
                                                                    </table>

                                                                    @php $medicineCabinetDataCount++ @endphp
                                                                    @if($medicineCabinetDataCount% 6 == 0 && sizeof($medicineCabinetData) != $medicineCabinetDataCount)
                                                                        <div class="page-break"></div>
                                                                        <table  style="border-bottom: 10px solid #fff;  background: #FAFAFA;">
                                                                            <tr>
                                                                                <td style="padding:10px; vertical-align: top;"></td>
                                                                            </tr>
                                                                        </table>

                                                                    @endif
                                                                @endforeach
                                                            <!-- Wellkabinet data loop - end -->                              
                                                            </td>
                                                        </tr> 
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!------ Wellkabinet data listing - end ------>
                                        
                                        <!--- page break after wellkabinet data ends - start --->  
                                        @if(!empty($interactionsData))
                                            <!--- page break if interaction details exist --->  
                                            <div class="page-break"></div>
                                        @endif
                                        <!--- page break after wellkabinet data ends - end --->  

                                    </td>    
                                </tr>  

                                <!-- Rx Interactions start -->
                                @if(!empty($interactionsData))
                                    <tr style="text-align:center;">
                                        <td style="padding-top: 20px;">Interaction report showing interaction of Rx with supplements and their severity</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @php $mainInteractionSection = 0 @endphp
                                            @foreach($interactionsData as $interactionHeaderName => $interactionParentData)
                                                <table valign="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;"  width="100%">
                                                    <tr>
                                                        <td style="color: #44546A; padding: 24px 22px;  font-size: 16px; text-align: center;">
                                                            {{$interactionHeaderName}} - <img style="vertical-align: middle; margin-bottom: -14px;" src="{{asset('images/beta-carotene.svg')}}" alt="beta carotene">  Interactions
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            @php $interactionsCount = 0 @endphp
                                                            @foreach($interactionParentData as $interactionChildDataName => $interactionChildKey)
                                                                <div style="padding-bottom:15px">
                                                                    <div class="{{$interactionParentData[$interactionChildDataName][0]['class']}}" style="font-size: 18px; ">
                                                                        <table cellpadding="0" cellspacing="0"  width="100%" style="padding:0">
                                                                            <tr>
                                                                                <td style="padding:0" width="50px">
                                                                                <div class="cabinet-acco-img"><img src="{{$interactionParentData[$interactionChildDataName][0]['interactionIcon']}}" alt="major"></div>
                                                                                </td>
                                                                                <td style="padding:0">
                                                                                    <div class="int-headeing">
                                                                                        <div class="cabinet-acco-title">{{$interactionChildDataName}}<br>
                                                                                            <span style="color: #4f4f4f;">Interaction Rating:<strong> {{ucfirst($interactionParentData[$interactionChildDataName][0]['interactionRating'])}}</strong></span>
                                                                                        </div>
                                                                                        <div class="combination">{{$interactionParentData[$interactionChildDataName][0]['interactionRatingText']}}</div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                                @php $interactionsCount++ @endphp
                                                                @if($interactionsCount% 10 == 0 && sizeof($interactionParentData) != $interactionsCount)
                                                                    <!--- page break to each set of interactions if more than 10 records code start --->  
                                                                    <div class="page-break"></div>
                                                                    <table valign="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;"  width="100%">
                                                                        <tr>
                                                                            <td style="color: #44546A; padding: 24px 22px;  font-size: 16px; text-align: center;"></td>
                                                                        </tr>
                                                                    </table>
                                                                    <!--- page break to each set of interactions if more than 10 records code end --->  
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                </table>
                                                @php $mainInteractionSection++ @endphp
                                                @if($mainInteractionSection != sizeof($interactionsData))
                                                    <!--- page break to each set of interactions code start --->  
                                                    <div class="page-break"></div>
                                                    <!--- page break to each set of interactions code end --->  
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                                <!-- Rx Interactions end -->

                               

                                <!--------------- Display last 5 Notes of each medicine data - code start ------------------>
                                @if(!empty($medicineCabinetNotes))

                                    <!------------ Check if notes added by user for any medicine data - code start ------------>
                                    <div class="page-break"></div>
                                    <!------------ Check if notes added by user for any medicine data - code end ------------>

                                    @foreach($medicineCabinetNotes as $medicineCabinetNotesKey => $medicineCabinetNotesValue)
                                        @php 
                                            $notesId = substr($medicineCabinetNotesKey, strpos($medicineCabinetNotesKey, '#')+1);
                                            $noteNameTitle = substr($medicineCabinetNotesKey, 0, strpos($medicineCabinetNotesKey, '#'));
                                        @endphp
                                        <table valign="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;"  width="100%">
                                            <tr>
                                                <td style="color: #44546A; padding: 24px 22px;  font-size: 16px; text-align: center;">
                                                    <a name="note{{$notesId}}">{{$noteNameTitle}} :  Notes</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div style="padding-bottom:15px">
                                                        @foreach($medicineCabinetNotesValue as $medicineCabinetNotesValueKey => $medicineCabinetNotesValueData)
                                                            <table cellpadding="0" cellspacing="0"  width="100%" style="padding:0; border-top: 1px solid #ddd;">
                                                                <tr>
                                                                    <td style="padding:0"> {{$medicineCabinetNotesValueData['date']}} </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding:0" width="50px"> {{$medicineCabinetNotesValueData['notes']}} </td>
                                                                </tr>
                                                            </table>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="page-break"></div>
                                    @endforeach
                                @endif
                                <!--------------- Display last 5 Notes of each medicine data - code end ------------------>

                            </tbody>
                        </table>
                    </td>
                    
                </tr>

            </tbody>
        </table>

        <footer>
            <!---- Footer start --->
            @include('page.reports.wellkabinet-pdf.footer')
            <!---- Footer end --->
        </footer>

    </body>
</html>