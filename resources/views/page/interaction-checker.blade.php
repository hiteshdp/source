@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp
                
@section('title', __('Wellkasa - Research Integrative and Natural Medicine - Personalized and Free!'))
@section('meta-keywords', __('Wellkasa, natural, personalized, supplements, herbs, nutrition, integrative, mind-body, evidence, interaction checker'))
@section('meta-news-keywords', __('Wellkasa, natural, personalized, supplements, herbs, nutrition, integrative, mind-body, evidence, interaction checker'))
@section('meta-description', __('Find evidence-informed nutrition, herbs, supplements, and mind-body therapies. Build personalized integrative care plans for you and your loved ones.'))

@section('twitter-url', Request::url())
@section('twitter-title', __('Wellkasa - Research Integrative and Natural Medicine - Personalized & Free!'))
@section('twitter-description'){!!'Wellkasa - Research Engine for evidence based information on effectiveness of nutrition, supplements, &amp; natural therapies for many diseases and conditions.'!!}@stop

@section('og-url', Request::url())
@section('og-title', __('Wellkasa - Research Integrative and Natural Medicine - Personalized & Free!'))
@section('fb-title', __('Wellkasa - Research Integrative and Natural Medicine - Personalized & Free!'))
@section('og-description'){!!'Wellkasa - Research Engine for evidence based information on effectiveness of nutrition, supplements, &amp; natural therapies for many diseases and conditions.'!!}@stop

@section('content')

<div class="container container750 rx">
    <div class="row pt-lg-5">
        <div class="col-lg-12 mx-auto">
            <div class="floating">
                <form method="post" id="reportInteraction">
                    <div class="right-search round-search text-left pt-2 mb-0">
                        <h3 class="save-action-input" id="interactionName" style="display: none;">
                            <a href="#">
                                <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.9999 6.99997H4.41394L9.70694 1.70697L8.29294 0.292969L0.585938 7.99997L8.29294 15.707L9.70694 14.293L4.41394 8.99997H18.9999V6.99997Z" fill="#44546A"/>
                                </svg>
                            </a>
                            <div class="report-save">
                                <input type="text" class="form-group reportNameInput" value="" name="reportName" autocomplete="off" placeholder="Type File Name to Save" id="reportName" required/> 
                                <input type="button" class="btn btn-primary btn-save-as" value="Save As" id="saveAs"/>
                                <input type="button" class="btn btn-primary btn-save-as" value="Save" style="display:none;" id="saveInteractions" />
                                <input type="button" class="btn btn-primary btn-save-as" style="display:none; color:#44546A;" value="Cancel" id="cancelInteractionName" />
                            </div>
                        </h3>
                        <div class="input-group rounded mt-1">
                            <span class="input-group-text border-0" id="search-addon">
                            <svg class="mx-auto" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 0C10.3008 0.0446294 6.76575 1.53395 4.14985 4.14985C1.53395 6.76575 0.0446294 10.3008 0 14C0.0446294 17.6992 1.53395 21.2343 4.14985 23.8501C6.76575 26.466 10.3008 27.9554 14 28C17.6992 27.9554 21.2343 26.466 23.8501 23.8501C26.466 21.2343 27.9554 17.6992 28 14C27.9554 10.3008 26.466 6.76575 23.8501 4.14985C21.2343 1.53395 17.6992 0.0446294 14 0ZM22 15H15V22H13V15H6V13H13V6H15V13H22V15Z" fill="#35C0ED"/>
                            </svg>
                            </span>
                            <input type="search" class="form-control interactionChecker" placeholder="Start by typing a drug or a natural medicine name" aria-label="Search" aria-describedby="search-addon" />
                        </div>
                        <div class="small-text">
                            <div class="deseases-list">
                            </div>
                        </div>
                        <div>
                            <div class="select-severity" style="display: none;">
                                <span class="severity-title mr-2">Select severity to show/hide</span>
                                <div class="form-group tick mx-2">
                                    <input type="checkbox" checked="checked" name="major" value="major" id="major"/>
                                    <label for="major">Major</label>
                                </div>

                                <div class="form-group tick bgyellow mx-2">
                                    <input type="checkbox" name="moderate" value="moderate" id="moderate">
                                    <label for="moderate">Moderate</label>
                                </div>

                                <div class="form-group tick bggreen mx-2">
                                    <input type="checkbox" name="mild" value="mild" id="mild">
                                    <label for="mild">Minor</label>
                                </div>
                            </div>
                            <div class="interactions-details"></div>

                            <div class="clarity-info">
                                <ul class="mt-4">
                                    <li>
                                        <a href="javascript:void(0)" onclick="showCheckerDetailsPopUp();">
                                            <span class="mr-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g transform="translate(-608 -1091)"><rect width="24" height="24" transform="translate(608 1091)" fill="none"/><path d="M4.444,18.315A10,10,0,1,1,10,20,10,10,0,0,1,4.444,18.315Zm.695-15.59A8.751,8.751,0,1,0,10,1.25,8.75,8.75,0,0,0,5.139,2.725Zm2.985,12.9a.625.625,0,0,1,0-1.25h1.25V8.813H8.749a.625.625,0,0,1,0-1.25h1.875v6.813h1.25a.625.625,0,0,1,0,1.25ZM9.105,5.282a.875.875,0,1,1,.874.874A.875.875,0,0,1,9.105,5.282Z" transform="translate(610 1093)" fill="#35c0ed"/></g></svg>
                                            </span>
                                            Checker details
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="showDisclaimerPopUp();">
                                            <span class="mr-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g transform="translate(-640 -1091)"><rect width="24" height="24" transform="translate(640 1091)" fill="none"/><path d="M8.11,19.608A1.643,1.643,0,0,1,7.549,18.6h3.236A1.642,1.642,0,0,1,9.2,20H9.173A1.64,1.64,0,0,1,8.11,19.608ZM.616,17.945A.615.615,0,0,1,0,17.33v-.837a.612.612,0,0,1,.055-.253.621.621,0,0,1,.154-.209,8.834,8.834,0,0,0,1.52-1.8A7.772,7.772,0,0,0,2.56,11.27V8.23A6.653,6.653,0,0,1,8.3,1.627V.822a.821.821,0,1,1,1.642,0v.794A6.65,6.65,0,0,1,15.752,8.23v3.04a7.788,7.788,0,0,0,.831,2.959,8.878,8.878,0,0,0,1.544,1.8.616.616,0,0,1,.21.462v.837a.616.616,0,0,1-.616.615ZM7.071,3.1A5.377,5.377,0,0,0,3.8,8.23v3.04a8.757,8.757,0,0,1-1.015,3.6,9.936,9.936,0,0,1-1.5,1.846H17.062a9.936,9.936,0,0,1-1.5-1.846,8.771,8.771,0,0,1-1.015-3.6V8.23A5.378,5.378,0,0,0,7.071,3.1Z" transform="translate(643 1093)" fill="#35c0ed"/></g></svg>
                                            </span>
                                            Disclaimer
                                        </a>
                                    </li>
                                    <li style="display: none;">
                                    
                                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><span class="mr-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g transform="translate(-675 -1091)"><rect width="24" height="24" transform="translate(675 1091)" fill="#fff"/><path d="M5.158,5H18.842A3.158,3.158,0,0,1,22,8.158v9.474a3.158,3.158,0,0,1-3.158,3.158H5.158A3.158,3.158,0,0,1,2,17.632V8.158A3.158,3.158,0,0,1,5.158,5Zm0,1.053a2.1,2.1,0,0,0-1.351.491L12,11.863l8.193-5.32a2.1,2.1,0,0,0-1.351-.491ZM12,13.118,3.194,7.4a2.1,2.1,0,0,0-.141.758v9.474a2.105,2.105,0,0,0,2.105,2.105H18.842a2.105,2.105,0,0,0,2.105-2.105V8.158a2.081,2.081,0,0,0-.141-.758L12,13.117Z" transform="translate(675 1090)" fill="#35c0ed"/></g></svg>
                                            </svg>
                                        </span>Share</a>
                                        <div class="dropdown-menu share-dropdown" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="{{route('saveInteractionPdf')}}">Save PDF</a>
                                            <a class="dropdown-item" href="javascript:void(0)">Email PDF</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!---Modal pop up for previous page redirection confirmation code start---->
<div class="modal fade" id="previousPageConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="interaction-modal-title">Warning</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="interaction-modal-body">
        You pressed a back button. Are you sure you want to go back?
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button"  id="modalYes" class="btn btn-green modalYes">Yes</button>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for previous page redirection confirmation code end---->

<!---Modal pop up for delete all confirmation code start---->
<div class="modal fade" id="deleteAllConfirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="delete-all-confirmation-modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="delete-all-confirmation-modal-body">
        
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button"  id="deleteAllYes" class="btn btn-green deleteAllYes">Yes</button>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for delete All confirmation code end---->

<!---Modal pop up for checker details code start--->
<div class="modal fade" id="checkerDetail">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow bg-white rounded">
      <div class="modal-header">
        <h4 class="modal-title" id="checkerdetail-modal-title">Checker details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="black"/>
                </svg>
          </span>
        </button>
      </div>
      <div class="modal-body" id="checkerdetail-modal-body">
            <h2>Interaction details</h2>
            <h3 class="ic-info red-dot">Major</h3>
            <p>Do not take this combination</p>
            <h3 class="ic-info yellow-dot">Moderate</h3>
            <p>Be cautious with this combination</p>
            <h3 class="ic-info green-dot">Minor</h3>
            <p>Be watchful with this combination</p>

            <div class="medicine-types">
                <h2>Medicine Types</h2>
                <div class="row pt-3">
                    <div class="col-12 col-lg-6">
                        <p class="pill natural-medicine">Natural Medicine</p>
                        <p class="pill drugs">Rx Drug</p>
                    </div>

                    <div class="col-12 col-lg-6">
                        <p class="pill yoga">Mind/Body Therapy</p>
                    </div>
                </div> 
            </div>
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for checker details code end--->

<!---Modal pop up for disclaimer code start--->
<div class="modal fade" id="showDisclaimer">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow bg-white rounded">
      <div class="modal-header">
        <h4 class="modal-title" id="showDisclaimer-modal-title">Disclaimer</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="black"/>
                </svg>
            </span>
        </button>
      </div>
      <div class="modal-body" id="showDisclaimer-modal-body">
          <p>The interaction checker does not check for drug-drug or supplement-supplement interactions. This is not an 
        all-inclusive comprehensive list of potential interactions and is for informational purposes only. Not all interactions 
        are known or well reported in the scientific literature, and new interactions are continually being reported. Input is 
        needed from a qualified healthcare provider including a pharmacist before starting any therapy. Application of 
        clinical judgement is necessary.</p>
            
      </div>
    </div>
  </div>
</div>
<!---Modal pop up for disclaimer code end--->

<!---Modal pop up for Level Of Evidence code start--->
<div class="modal fade" id="showLevelOfEvidence">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow bg-white rounded">
            <div class="modal-header">
                    <h4 class="modal-title" id="showLevelOfEvidence-modal-title">Level of Evidence</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.7439 11.1157L21.955 1.90455C22.1371 1.69199 22.2322 1.41857 22.2214 1.13893C22.2106 0.859291 22.0947 0.594026 21.8968 0.396143C21.6989 0.19826 21.4336 0.0823348 21.154 0.0715335C20.8744 0.0607322 20.6009 0.155851 20.3884 0.33788L11.1773 9.54899L1.96616 0.326769C1.75693 0.117542 1.47316 0 1.17727 0C0.88138 0 0.597608 0.117542 0.388382 0.326769C0.179155 0.535995 0.0616131 0.819767 0.0616131 1.11566C0.0616131 1.41155 0.179155 1.69532 0.388382 1.90455L9.6106 11.1157L0.388382 20.3268C0.272069 20.4264 0.177602 20.549 0.110909 20.6868C0.0442165 20.8247 0.00673854 20.9748 0.00082799 21.1278C-0.00508256 21.2808 0.0207013 21.4334 0.0765619 21.576C0.132423 21.7186 0.217154 21.8481 0.325437 21.9564C0.43372 22.0647 0.563217 22.1494 0.7058 22.2053C0.848384 22.2611 1.00098 22.2869 1.154 22.281C1.30702 22.2751 1.45717 22.2376 1.59501 22.1709C1.73286 22.1042 1.85544 22.0098 1.95505 21.8934L11.1773 12.6823L20.3884 21.8934C20.6009 22.0755 20.8744 22.1706 21.154 22.1598C21.4336 22.149 21.6989 22.0331 21.8968 21.8352C22.0947 21.6373 22.2106 21.372 22.2214 21.0924C22.2322 20.8127 22.1371 20.5393 21.955 20.3268L12.7439 11.1157Z" fill="black"/>
                            </svg>
                        </span>
                    </button>
            </div>
            <div class="modal-body" id="showLevelOfEvidence-modal-body">
                
                <div align="center">
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td valign="top" style="padding:0;">
                                    <table border="0" cellspacing="6" cellpadding="0" style="width:100%;">
                                        <tbody>
                                            <tr>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;"><b>Level</b></p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;"><b> Definition</b></p>
                                                </td>
                                            </tr>
                                            <tr id="a1" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">A</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">High-quality randomized controlled trial (RCT)</p>
                                                </td>
                                            </tr>
                                            <tr id="a2" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">A</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">High-quality meta-analysis (quantitative systematic review)</p>
                                                </td>
                                            </tr>
                                            <tr id="b1" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">B</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Nonrandomized clinical trial</p>
                                                </td>
                                            </tr>
                                            <tr id="b2" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">B</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Nonquantitative systematic review</p>
                                                </td>
                                            </tr>
                                            <tr id="b3" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">B</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Lower quality RCT</p>
                                                </td>
                                            </tr>
                                            <tr id="b4" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">B</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Clinical cohort study</p>
                                                </td>
                                            </tr>
                                            <tr id="b5" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">B</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Case-control study</p>
                                                </td>
                                            </tr>
                                            <tr id="b6" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">B</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Historical control</p>
                                                </td>
                                            </tr>
                                            <tr id="b7" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">B</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Epidemiologic study</p>
                                                </td>
                                            </tr>
                                            <tr id="c1" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">C</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Consensus</p>
                                                </td>
                                            </tr>
                                            <tr id="c2" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">C</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Expert opinion</p>
                                                </td>
                                            </tr>
                                            <tr id="d1" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">D</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Anecdotal evidence</p>
                                                </td>
                                            </tr>
                                            <tr id="d2" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding: 0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">D</p>
                                                </td>
                                                <td style="padding: 0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">In vitro or animal study</p>
                                                </td>
                                            </tr>
                                            <tr id="d3" class="levelOfEvidenceDefinitionContent">
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">D</p>
                                                </td>
                                                <td style="padding:0.75pt;">
                                                    <p style="font-size:11pt;margin:0;">Theoretical based on pharmacology</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    
            </div>
        </div>
    </div>
</div>
<!---Modal pop up for Level Of Evidence code end--->

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.0/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.0/jspdf.debug.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.0/html2canvas.js" ></script>
<script type="text/javascript">

    $(document).on("load", function() {
        location.reload(true);
    });

    // on back button event, clear all interactions data from the local storage
    $(function() {
        window.history.pushState({page: 1}, "","");
        window.onpopstate = function(event) {
            if (window.performance && window.performance.navigation.type == window.performance.navigation.TYPE_BACK_FORWARD || event.currentTarget.performance.navigation.type == 2) {
                $('#previousPageConfirmation').modal('show');
                
                $('.modalYes').on('click',function()
                {
                    deleteAll();
                    $('#previousPageConfirmation').modal('hide');
                    window.location.href = "{{ route('my-wellkasa-rx') }}"
                });
                history.pushState(null,  document.title, location.href); 

            }else{
                $('#previousPageConfirmation').modal('show');
                
                $('.modalYes').on('click',function()
                {
                    deleteAll();
                    $('#previousPageConfirmation').modal('hide');
                    window.location.href = "{{ route('my-wellkasa-rx') }}"
                });
                history.pushState(null,  document.title, location.href); 

            }
        }
    });
    function showPopover(){
        $('[data-toggle="popover"]').popover({
            html:true
        });
    }

    showSeveritySelections(); // show select hide/show severity checkboxes

    function showSeveritySelections() {
        // if there are interactions data available then show severity options
        if($(".interactions-details").html() != '') {
          
          $(".select-severity").show(); // show severity checkbox
          
          // if high severity available then show major checkbox checked else unchecked
          if($("div.severity-high").length !=0) {
            $('#major').prop("checked", true);
          }else{
            $('#major').prop("checked", false);
          }

          // if moderate severity available then show moderate checkbox checked else unchecked
          if($("div.severity-moderate").length!=0){
            $('#moderate').prop("checked", true);
          }else{
            $('#moderate').prop("checked", false);
          }

          // if mild severity available then show minor checkbox checked else unchecked
          if($("div.severity-mild").length !=0){
            $('#mild').prop("checked", true);
          }else{
            $('#mild').prop("checked", false);
          }
          
        }else{
            $(".select-severity").hide(); // hide severity checkbox
        }
        showPopover(); // show popover
    }

    // hide/show major details
    $('#major').change(function() {

        // if checked major then show major details else hide details
        if ($('input#major').is(':checked')) {
            $(".severity-high").show();
        }else{
            $(".severity-high").hide();
        }

        // if checked mild then show mild details else hide details
        if ($('input#mild').is(':checked')) {
            $(".severity-mild").show();
        }else{
            $(".severity-mild").hide();
        }

        // if checked moderate then show moderate details else hide details
        if ($('input#moderate').is(':checked')) {
            $(".severity-moderate").show();
        }else{
            $(".severity-moderate").hide();
        }
    });

    // hide/show moderate details
    $('#moderate').change(function() {
        // if checked then show moderate details else hide details
        if ($('input#moderate').is(':checked')) {
            $(".severity-moderate").show();
        }else{
            $(".severity-moderate").hide();
        }

        // if mild checked then show mild details else hide details
        if ($('input#mild').is(':checked')) {
            $(".severity-mild").show();
        }else{
            $(".severity-mild").hide();
        }
       
        // if major checked then show major details else hide details
        if ($('input#major').is(':checked')) {
            $(".severity-high").show();
        }else{
            $(".severity-high").hide();
        }
    });

    // hide/show mild details
    $('#mild').change(function() {
        
        // if checked mild then show mild details else hide details
        if ($('input#mild').is(':checked')) {
            $(".severity-mild").show();
        }else{
            $(".severity-mild").hide();
        }

        // if checked major then show major details else hide details
        if ($('input#major').is(':checked')) {
            $(".severity-major").show();
        }else{
            $(".severity-major").hide();
        }

        // if checked moderate then show moderate details else hide details
        if ($('input#moderate').is(':checked')) {
            $(".severity-moderate").show();
        }else{
            $(".severity-moderate").hide();
        }
    });

    addDrugsNmData();


    function showSaveInteractionReportName(){
        if($(".deseases-list").html() != ''){
            $("#interactionName").show();
        }else{
            $("#interactionName").hide();
        }
    }

    function showDetailsFooter(){
        if($(".deseases-list").html() != ''){
            $('.clarity-info').show();
        }else{
            $('.clarity-info').hide();
        }
    }

   // Auto complete ajax call 
   var path_therapy = "{{ route('get-drugs-nm-list') }}";
  
   $('input.interactionChecker').typeahead({
      items:'all',
      source: function (query, process) {
        
        // Get drug id in localstorage
        var drugsDataIds = JSON.parse(localStorage.getItem("drugsDataId"));
        var naturalMedicinesDataIds = JSON.parse(localStorage.getItem("naturalMedicinesDataId"));

         return $.ajax({
            url: path_therapy,
            type: 'get',
            data: { query: query,drugsDataIds: drugsDataIds,naturalMedicinesDataIds:naturalMedicinesDataIds},
            dataType: 'json',
            success: function (result) {
               var resultList = result.map(function (item) {
                     var aItem = { id: item.Id, name: item.Name, class: item.Class };
                     return JSON.stringify(aItem);
               });
               return process(resultList);
            }
         });
      },

      matcher: function (obj) {
         var item = JSON.parse(obj);
         return ~item.name.toLowerCase().indexOf(this.query.toLowerCase())
      },

      sorter: function (items) {          
         var beginswith = [], caseSensitive = [], caseInsensitive = [], item;
         while (aItem = items.shift()) {
            var item = JSON.parse(aItem);
            if (!item.name.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(JSON.stringify(item));
            else if (~item.name.indexOf(this.query)) caseSensitive.push(JSON.stringify(item));
            else caseInsensitive.push(JSON.stringify(item));
         }
         return beginswith.concat(caseSensitive, caseInsensitive)
      },

      highlighter: function (obj) {
         var item = JSON.parse(obj);
         var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
         var mainClass =  item.name.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
            return '<strong>' + match + '</strong>'
         })
        return '<span class='+item.class+'>' + mainClass + '</span>';
      },

      updater: function (obj,process) {
        var item = JSON.parse(obj);
        $('input.interactionChecker').attr('value', item.id);
        
        let resultResponse = '';
        
        //Merged Logic to identify that it's condition or Therapy and then redirect accordingly - Start
        if(item.id.includes("naturalMedicine")){
            item.id = item.id.replace("-naturalMedicine", "");
            var dataSend = { medicineId: item.id };   
        }
        else{
            item.id = item.id.replace("-drugs", "");
            var dataSend = { drugsId: item.id };
        }
        let showInteractionsUrl = "{{route('show-interactions')}}";
         $.ajax({
            url: showInteractionsUrl,
            type: 'get',
            data: dataSend,
            dataType: 'json',
            success: function (result) {

                var response = [];
                response[0] = result.section;
                

                // logic for store drugsDataId in localstorage
                if(result.drugsDataId != undefined && result.drugsDataId != ''){
                    if(localStorage.getItem("drugsDataId") === null) {
                        var drugsDataIdArray = [];
                        drugsDataIdArray.push(result.drugsDataId);
                        localStorage.setItem("drugsDataId",JSON.stringify(drugsDataIdArray));
                        
                    }else{
                        var oldArr = JSON.parse(localStorage.getItem("drugsDataId"));
                        oldArr.push(result.drugsDataId);
                        localStorage.setItem("drugsDataId",JSON.stringify(oldArr));
                    }
                }


                // Logic for store natural Medicines Data in localstorage
                if(result.naturalMedicinesDataId != undefined && result.naturalMedicinesDataId != ''){
                    if(localStorage.getItem("naturalMedicinesDataId") === null) {
                        var naturalMedicinesDataIdArray = [];
                        naturalMedicinesDataIdArray.push(result.naturalMedicinesDataId);
                        localStorage.setItem("naturalMedicinesDataId",JSON.stringify(naturalMedicinesDataIdArray));
                        
                    }else{
                        var oldNaturalArr = JSON.parse(localStorage.getItem("naturalMedicinesDataId"));
                        oldNaturalArr.push(result.naturalMedicinesDataId);
                        localStorage.setItem("naturalMedicinesDataId",JSON.stringify(oldNaturalArr));
                    }
                }



                // check if localStorage have not drug data then add in localStorage, else append the values
                if(localStorage.getItem("drugs-nm-selected-data") === null) {
                    localStorage.setItem("drugs-nm-selected-data", JSON.stringify(response));
                }else{
                    existing = JSON.parse(localStorage.getItem("drugs-nm-selected-data"));
                    if(existing){
                        var oldArr = JSON.parse(localStorage.getItem("drugs-nm-selected-data"));
                        oldArr.push(result.section)
                        localStorage.setItem("drugs-nm-selected-data", JSON.stringify(oldArr));
                    }

                }
                addDrugsNmData();

                var getNewDrugsDataId = JSON.parse(localStorage.getItem("drugsDataId"));
                var getNewNaturalMedicinesDataId = JSON.parse(localStorage.getItem("naturalMedicinesDataId"));
                var requestData = { getNewDrugsDataId: getNewDrugsDataId, getNewNaturalMedicinesDataId: getNewNaturalMedicinesDataId };
                let getInteractionsUrl = "{{route('get-interactions')}}";
                $.ajax({
                    url: getInteractionsUrl,
                    type: 'get',
                    data: requestData,
                    dataType: 'json',
                    success: function (result) {
                        localStorage.setItem("interactions-details", JSON.stringify(result));
                        addDrugsNmData();
                    }
                });
                
            }
        });
        //Merged Logic to identify that it's condition or Therapy and then redirect accordingly - End

        return '';
      }

   });

    function addDrugsNmData(){
        // store the drug / natural medicine list under search bar
        if(JSON.parse(localStorage.getItem("drugs-nm-selected-data")) != null){
            var storedNames = JSON.parse(localStorage.getItem("drugs-nm-selected-data"));
            $(".deseases-list").html('');
            for (const element of storedNames) {
                $(".deseases-list").append(element);
            }
        }

        if(JSON.parse(localStorage.getItem("interactions-details")) != null){
            var storedInteractionsNames = JSON.parse(localStorage.getItem("interactions-details"));
            $(".interactions-details").html('');
            $(".interactions-details").html(storedInteractionsNames)
        }   
        showDeleteAllButton(); // check if drugs or natural medicine data is more than 3, then show all button to delete 
        showSeveritySelections(); // show severity checkbox selections if there are any interactions
        showSaveInteractionReportName(); // display input field to save interaction report name
        showDetailsFooter() // display checker details , disclaimer and share option under interaction details
    }

    function deleteDrugs(id){

        let idName = $(id).attr('data-drug'); // get the drug id from the data-drug attribute
        var valueToRemove = document.getElementById(idName).outerHTML; // get the html tag containing drug detail to remove 

         // get data of selected drugs tags under search bar
        let selectedDrugsNmTags = JSON.parse(localStorage.getItem("drugs-nm-selected-data"));
        
        // removes drugs tags under search bar
        for(let drugsTagKey in selectedDrugsNmTags){
            //check if drugs tags is same as the valueToRemove variable then delete that drug tag
            if(selectedDrugsNmTags[drugsTagKey] == valueToRemove){
                selectedDrugsNmTags.splice(drugsTagKey,1); // removes the drugs tags
                break;
            }
        }
        localStorage.setItem('drugs-nm-selected-data', JSON.stringify(selectedDrugsNmTags)); // updates the selected drugs listing


        // get drug id from data-drug attr and convert it into number
        var getdrugId = idName;
        var drugIds = getdrugId.match(/(\d+)/);
        let drugId = drugIds[0];

        // get data drug ids form drugsDataIds localStorage array
        let removeDrugsDataIds = JSON.parse(localStorage.getItem("drugsDataId"));
        
        
        // removes natural medicine ids localStorage
        for(var drugsIdkey in removeDrugsDataIds){
            //check if natural medicine is same as the nmId variable then delete that natural medicine tag
            if(removeDrugsDataIds[drugsIdkey] == drugId){
                removeDrugsDataIds.splice(drugsIdkey,1); // removes the drug id from the localstorage 
                break;
            }
        }
        localStorage.setItem('drugsDataId', JSON.stringify(removeDrugsDataIds));// updates the selected drugs ids listing


        var getNewDrugsDataId = JSON.parse(localStorage.getItem("drugsDataId"));
        var getNewNaturalMedicinesDataId = JSON.parse(localStorage.getItem("naturalMedicinesDataId"));
        var requestData = { getNewDrugsDataId: getNewDrugsDataId, getNewNaturalMedicinesDataId: getNewNaturalMedicinesDataId };
        let getInteractionsUrl = "{{route('get-interactions')}}";
        $.ajax({
            url: getInteractionsUrl,
            type: 'get',
            data: requestData,
            dataType: 'json',
            success: function (result) {
                localStorage.setItem("interactions-details", JSON.stringify(result));
                addDrugsNmData();
            }
        });
    }

    function deleteNaturalMedicine(id){

        let idName = $(id).attr('data-nm'); // get the naturalMedicine id from the data-nm attribute
        var nmTagsToRemove = document.getElementById(idName).outerHTML; // get the html tag containing natural medicine detail to remove 
        // nmTagsToRemove = JSON.stringify(nmTagsToRemove);

       
        // get data of selected drugs tags under search bar
        let selectedDrugsNmTags = JSON.parse(localStorage.getItem("drugs-nm-selected-data"));

        // removes natural medicine tags under search bar
        for(var tagskey in selectedDrugsNmTags){
            //check if natural medicine is same as the valueToRemove variable then delete that natural medicine tag
            if(selectedDrugsNmTags[tagskey] == nmTagsToRemove){
                selectedDrugsNmTags.splice(tagskey,1); // removes the natural medicine tags
                break;
            }
        }
        localStorage.setItem('drugs-nm-selected-data', JSON.stringify(selectedDrugsNmTags)); // updates the selected natural medicine listing


        // get nm-id and convert it into number
        var getNmId = idName;
        var nmIds = getNmId.match(/(\d+)/);
        let nmId = nmIds[0];

        // get natural medicine ids form naturalMedicinesDataId localStorage array
        var removeNaturalMedicinesIds = JSON.parse(localStorage.getItem("naturalMedicinesDataId"));
        
        // removes natural medicine ids localStorage
        for(var nmkey in removeNaturalMedicinesIds){
            //check if natural medicine is same as the nmId variable then delete that natural medicine tag
            if(removeNaturalMedicinesIds[nmkey] == nmId){
                removeNaturalMedicinesIds.splice(nmkey,1); // removes the natural medicine id from localStorage
                break;
            }
        }
        localStorage.setItem('naturalMedicinesDataId', JSON.stringify(removeNaturalMedicinesIds));// updates the selected natural medicine ids listing


        var getNewDrugsDataId = JSON.parse(localStorage.getItem("drugsDataId"));
        var getNewNaturalMedicinesDataId = JSON.parse(localStorage.getItem("naturalMedicinesDataId"));
        var requestData = { getNewDrugsDataId: getNewDrugsDataId, getNewNaturalMedicinesDataId: getNewNaturalMedicinesDataId };
        let getInteractionsUrl = "{{route('get-interactions')}}";
        $.ajax({
            url: getInteractionsUrl,
            type: 'get',
            data: requestData,
            dataType: 'json',
            success: function (result) {
                localStorage.setItem("interactions-details", JSON.stringify(result));
                addDrugsNmData();
            }
        });
    }

    // function to show delete all button if the selected drugs or natural medicines are more than equals to 3
    function showDeleteAllButton(){
        
        const drugsNmData = $('.conditionTags').length;
        var html = '';

        if(drugsNmData >= 3){
            html += '<span class="btn btn-outline-dark btn-sm mb-2 mr-2" id="deleteAll">';
            html += '<a title="Delete all" href="javascript:void(0);"> all</a>';
            html += '<a onclick="deleteAllSelectedData(this);" title="Delete all selected drugs and natural medicine interactions" class="close-btn" aria-label="Close"><span aria-hidden="true">×</span></a>';
            html += ' </span>';
            $('.deseases-list').prepend(html);
        }
    }

    //----------------------------------  Delete All Selected data Pop up code start--------------------------- //
    function deleteAllSelectedData(){
        // Set modal title
        $('#delete-all-confirmation-modal-title').html('Delete All Confirmation');
        
        // Set body
        $('#delete-all-confirmation-modal-body').html('Are you sure you want to delete all selected data?');

        // Show Modal
        $('#deleteAllConfirmation').modal('show');
    }

    // Call Ajax for Delete Interaction
    $('.deleteAllYes').on('click',function()
    {     
        deleteAll(); // delete all selected drugs/natural medicines
        $('#deleteAllConfirmation').modal('hide'); // hide delete all Modal pop up
    });
    //----------------------------------  Delete All Selected data Pop up code end--------------------------- //

    // function of implementing logic code for delete all selected drugs or natural medicines and its interactions-details
    function deleteAll(){

        // delete all selected drugs ids
        localStorage.removeItem("drugsDataId"); // remove drug Ids from localStorage
        
        // delete all selected natural medicines ids
        localStorage.removeItem("naturalMedicinesDataId"); // remove natural medicine Ids from localStorage

        // delete all selected drugs & natural medicines ids
        localStorage.removeItem("drugs-nm-selected-data"); // remove drugs & natural medicine Ids from localStorage

        $(".deseases-list").html(''); // remove all selected drugs and natural medicine details from html document

        // delete all interactions details
        localStorage.removeItem("interactions-details"); // remove interaction details from localStorage
        $(".interactions-details").html(''); // remove interaction details from html document

        showSeveritySelections(); // show severity checkbox selections if there are any interactions
        showSaveInteractionReportName(); // display input field to save interaction report name
        showDetailsFooter() // display checker details , disclaimer and share option under interaction details
    }
 
    // Show save & cancel button
    $( "#reportName , #saveAs" ).click(function(e) {
        $(".report-save").addClass('report-save-cancel');
        $( "#saveInteractions" ).show();
        $( "#cancelInteractionName" ).show();
        $( "#saveAs" ).hide();
        $("#reportName").prop("disabled",false);
    });
    // Hide save & cancel button 
    $( "#cancelInteractionName" ).click(function(e) {
        $(".report-save").removeClass('report-save-cancel');
        $( "#saveInteractions" ).hide();
        $( "#cancelInteractionName" ).hide();
        $( "#saveAs" ).show();
        $("#reportName-error").hide();
    });
    // Save interaction details
    $( "#saveInteractions" ).click(function(e) {
        
        let reportNameVal = $("#reportName").val();
        
        // validate reportName field
        if(reportNameVal == "") {
            $("[name='reportName']").valid();
            return false;
        }

        let saveInteractionsUrl = "{{route('save-interactions')}}";
        var csrf_token = $('meta[name="csrf-token"]').attr('content');

        var drugIds = JSON.parse(localStorage.getItem("drugsDataId"));
        var naturalMedicineIds = JSON.parse(localStorage.getItem("naturalMedicinesDataId"));
        var reportName = $("#reportName").val();
        var dataSend = {  _token: csrf_token, drugIds: drugIds, naturalMedicineIds: naturalMedicineIds, reportName: reportName };
        $.ajax({
            url: saveInteractionsUrl,
            type: 'post',
            data: dataSend,
            dataType: 'json',
            success: function (result) {
                if(result.status == 1){
                    deleteAll();
                    window.location.href = "{{ route('my-wellkasa-rx') }}";
                }else{
                    location.reload();
                }                
            },
            error: function (jqXHR,error) {
                if(jqXHR.status == 401){
                window.location.href = "{{ route('login') }}";
                }
                if(jqXHR.responseJSON !== undefined && jqXHR.responseJSON.message == "Your email address is not verified."){
                window.location.href = "{{ url('/email/verify') }}";  
                }
            }
        });
    })

    // Display checker details pop up
    function showCheckerDetailsPopUp(){
        $('#checkerDetail').modal('show');
        $('.modal-backdrop').attr('style','opacity:0 !important;');
    }

    // Display disclaimer pop up
    function showDisclaimerPopUp(){
        $('#showDisclaimer').modal('show');
        $('.modal-backdrop').attr('style','opacity:0 !important;');
    }
    
   // Display Level Of Evidence pop up
   function showLevelOfEvidencePopUp(id){
        // Removes previous highlighted level of evidence definition
        $(".levelOfEvidenceDefinitionContent").removeAttr('style');

        var trId = $(id).attr('id'); // gets the tr id of the level of evidence definition
        $("#"+trId).css('background-color','yellow'); // applies highlight of the selected level of evidence definition in popup

        $('#showLevelOfEvidence').modal('show');
        $('.modal-backdrop').attr('style','opacity:0 !important;');
    } 

</script>
<style>
@media print {
    .rx .form-group {
        display: none;
        
}
}
</style>    

@endpush