<html>
    <head>
        @include('page.reports.interaction-report.head')
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
                                                    <td  style="text-align: center; font-family: Prata; font-size: 24px; color: #44546A; padding-bottom: 14px;">
                                                    
                                                        Interaction Report
                                                    
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table align="top" cellpadding="0" cellspacing="0"  width="100%">
                                                            <tr>
                                                                <td style="text-align: right; font-size: 12px; padding-right: 10px; color: #44546A; width:50%">
                                                                    Created by:  {{$finalArray['createdBy']}}
                                                                </td>
                                                                <td style="text-align: left; font-size: 12px; padding-left: 10px; color: #44546A; width:50%">
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
                                        <table align="top" cellpadding="0" cellspacing="0" style="padding: 0 50px;"  width="100%">
                                            <tr>
                                                <td style="padding-top:24px; padding-bottom:10px; font-weight: 700; color: #44546A; font-family: Lato-Bold; font-size: 16px;">  
                                                    Interaction elements
                                                </td>    
                                            </tr> 
                                            <tr>
                                                <td style="padding-bottom:10px; font-weight: bold; color: #7380B4; font-size: 12px;">  
                                                <img style="vertical-align: sub; padding-right: 10px;" src="{{asset('images/pill1.svg')}}" alt="Wellkasa" title="pill1">  Natural therapies
                                                </td>    
                                            </tr> 
                                            <tr>
                                                <td style="padding-bottom:10px;  color: #44546A; font-size: 12px;">  
                                                    {{$finalArray['therapyName']}}
                                                </td>    
                                            </tr> 
                                            <tr>
                                                <td style="padding-bottom:10px; padding-top:15px; font-weight: bold; color: #7380B4; font-size: 12px;">  
                                                <img style="vertical-align: sub; padding-right: 10px;" src="{{asset('images/drug.svg')}}" alt="Wellkasa" title="drug">  Drugs
                                                </td>    
                                            </tr> 
                                            <tr>
                                                <td style="padding-bottom:10px;  color: #44546A; font-size: 12px;">  
                                                    {{$finalArray['drugName']}}
                                                </td>    
                                            </tr>
                                            <tr>
                                                <td style="background: #FDCA40; height: 1px; line-height: 1px;">  
                                                    &nbsp;
                                                </td>    
                                            </tr>  
                                        </table>   
                                        <?php 
                                            // Check if interactions record exists then display interaction data, else print appropriate message.
                                            if((array_key_exists("interactions",$finalArray)) && ($finalArray['interactions'] !='')) {
                                                
                                                // Display the interaction data
                                                echo nl2br($finalArray['interactions']);

                                            }else{
                                                
                                                // Execute this code when natural therapy / drugs is missing to showcase any interactions
                                                echo "<table align='top' cellpadding='0' cellspacing='0' style='padding: 50px 50px;'  width='100%'>";
                                                    echo "<tr>";
                                                        echo "<td style='padding-bottom:10px; text-align:center; color: #44546A; font-size: 12px;'>";
                                                            echo "Missing Natural therapies / Drugs data to display any interactions";
                                                        echo "</td>";
                                                    echo "</tr>";
                                                echo "</table>";
                                            }
                                        ?>
                                        
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
            @include('page.reports.interaction-report.footer')
            <!---- Footer end --->
        </footer>

        <!---- Join Wellkasa Page Template start --->
         @include('page.reports.join')
        <!---- Join Wellkasa Page Template end --->
    </body>
</html>