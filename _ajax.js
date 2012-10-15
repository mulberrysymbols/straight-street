//Straight Street

/* ##########################################################################################
	Global Vars
   ########################################################################################## */

//these vars are used as counters so can see when to call the DragDrop activation code for divs
//(only when last div in group is done, should activation code be called.

var	reviewThumbsCount = 0;
var	reviewThumbsCountMax = 2;
var	liveNonLiveThumbsCount = 0;
var	liveNonLiveThumbsCount = 0;
var	liveNonLiveThumbsCountMax = 2;
var	doReviewThumbsCount = 0;
var	doReviewThumbsCount = 1;


/* ##########################################################################################
	Functions
   ########################################################################################## */

function tryLogin(strObjUid,strObjUpass,strObjURemember)
{
	//get objects from name first
	var objUid = document.getElementById(strObjUid);		//_getElm(strObjUid);
	var objUpass = document.getElementById(strObjUpass);		//_getElm(strObjUpass);	
	//var objChkRemember = document.getElementById(strObjURemember);	//_getElm(strObjUpass);	

	//now get values
	var strUid = objUid.value;
	var strUpass = objUpass.value;
	//var strUrem = objChkRemember.checked;

	//alert("remember:"+strUrem);

	if ((strUid) && (strUpass)) {

	//attempt login
	//alert('Login: '+strUid+', Pass: '+strUpass);


	var httpREAD = getHTTPObject();

	//invoke PHP to see if login details are correct
	var now = new Date();
	httpREAD.open("GET", "db_tryLogin.php?uid=" + strUid + "&pass=" + strUpass + "&ms=" + now.getTime(), true);

	// handle what to do with the feedback 
	httpREAD.onreadystatechange = function () {
		if (httpREAD.readyState == 4) 
  		{
			//capture value (0 or 1)
			var sResult = httpREAD.responseText
			//alert(httpREAD.responseText);
			
			if (sResult=='1')
			{
				//alert('Yes');
				//document.cookie = 'ss_userid=test; expires=Fri, 3 Aug 2001 20:47:11 UTC; path=/';
				//setcookie("ss_userid", "$r[0]", time()+(3600*100));

				//Remember me?
				//if (strUrem=='true')
				//{
					document.cookie = 'ss_userid=' + strUid + '; expires=Sat, 1 Jan 2050 07:00:00 UTC; path=/';
				//}
				//else
				//{
				//	document.cookie = 'ss_userid=' + strUid + '; ; path=/';
				//}

				//document.location.reload();
				//dont just reload, go to home
				document.location= "/";
			}
			else
			{
				alert('Username and/or Password incorrect.');
			}
		}
	}
 	
	httpREAD.send(null);

	} else {

		alert('Please enter a username and password');

	}
}

function logout()
{
	//document.cookie = 'ss_userid=;;';
	eraseCookie('ss_userid');
	eraseCookie('ss_cart_contents');
	document.location.href='/';
}

function trim(str)
{
	return str.replace(/^\s*|\s*$/g,"");
}

function tagsHTML(strTags, bModify)
{
	// TODO: escape each tag
	var strTagsHTML = '<form><textarea id="preview_tags_text" readonly=readonly>'+strTags+'</textarea>'; 
	strModifyHTML  = '';
	if (bModify)
	{
		var arTags = strTags.split(',');
		if (trim(strTags).length != 0 )
		{
			strModifyHTML += '<br/><select id="preview_deltag"><option>' + arTags.join('</option><option>') + '</option></select>' ;
			strModifyHTML += '<a href="javascript:javascript:delTag(\'preview_image_id\',\'preview_deltag\');">  Delete Tag</a>&nbsp;';
		}
		else
			strModifyHTML += '&nbsp;<br/>';
		strModifyHTML += '<br/><input type="text" id="preview_newtag" maxlength="15"><a href="javascript:addTag(\'preview_image_id\',\'preview_newtag\');">  Add Tag</a>' +
	'<br/><a href="javascript:ad_edMedia(\'preview_image_id\');">Edit Media</a></form>';

	}
	return strTagsHTML + strModifyHTML;
}


function thumb_preview(showOrHide,newImgId,newCapt,newImg,newLicCapt,newLicIcon,newSponIcon, bEditTags, bRated)
{
	//get objects from name first
	var objPreviewDiv = document.getElementById("preview");
    var objPreviewDivRatedImg = document.getElementById("preview_rated_icon");

	//check if DIV is locked before continuing
	if (objPreviewDiv.className == "")
	{
		if  (!showOrHide)
		{
			objPreviewDiv.style.visibility = 'hidden';
            objPreviewDivRatedImg.style.visibility = 'hidden';
		}
		else
		{
		
			var objPreviewDivImgId = document.getElementById("preview_image_id");
			var objPreviewDivImg = document.getElementById("preview_image");
			var objPreviewDivCaption = document.getElementById("preview_caption");
			var objPreviewDivLicImg = document.getElementById("preview_lic_icon");
			var objPreviewDivSponImg = document.getElementById("preview_spon_icon");
			var objPreviewDivTags = document.getElementById("preview_tags");
			var newTags = getImgObj("thumbs", newImgId).getAttribute('tags');

			objPreviewDivImgId.value = newImgId;
			objPreviewDivCaption.innerHTML = newCapt;
			objPreviewDivImg.src = newImg;

			//no longer store lic image in DB - auto detect
			objPreviewDivLicImg.src = newLicIcon;
			objPreviewDivLicImg.title = newLicCapt;
			objPreviewDivLicImg.alt = newLicCapt;

            objPreviewDivRatedImg.style.visibility = (bRated) ? 'visible' : 'hidden';

			//objPreviewDivSponImg.src = newSponIcon;
			
			objPreviewDivTags.innerHTML = tagsHTML(newTags, bEditTags);

			objPreviewDiv.style.visibility = 'visible';
		}
	}
}

function thumb_preview_toggle(imgId, bFreeze)
{
	//-------
	//Toggle 
	if (bFreeze) 
	{
		var objPreviewDiv = document.getElementById("preview");
		if (objPreviewDiv.className == "")
		{
			objPreviewDiv.className = "locked";
		} else {
			objPreviewDiv.className = "";
		}
	}

	//-------
	//Add to Cart
	else
	{
//		var oblna = document.getElementById("licence_not_accepted")
		if (imgId != '')
		{
//			oblna.style.display = 'none'
			addImgToCart(imgId);
			updateGalleryCartNumItems();
		}
		else
		{
//			oblna.style.display = 'inline'
		}
	}

}

function thumb_preview_doreview(showOrHide,objImg,newImgId,newCapt,newImg,bAccept,sComments, bReadOnly)
{
	//get objects from name first
	var objPreviewDiv = document.getElementById("previewforreview");

	//check if DIV is locked before continuing
	if (objPreviewDiv.className == "")
	{
		var objPreviewDivImgId = document.getElementById("preview_image_id");
		var objPreviewDivImg = document.getElementById("preview_image");
		var objPreviewDivCaption = document.getElementById("preview_caption");
		//var objPreviewDivLicImg = document.getElementById("preview_lic_icon");
		//var objPreviewDivTags = document.getElementById("preview_tags");

		var ObjPreviewDivAccept = document.getElementById('radioAccept');
		var ObjPreviewDivDecline = document.getElementById('radioDecline');
		var ObjPreviewDivComments = document.getElementById('comments');
		var ObjPreviewDivSave = document.getElementById('save');
		if (showOrHide)
		{
         	//set fields
			objPreviewDivImgId.value = newImgId;
			objPreviewDivCaption.innerHTML = newCapt;
			objPreviewDivImg.src = newImg;
			ObjPreviewDivAccept.checked = (bAccept=='1');
			ObjPreviewDivDecline.checked = (bAccept=='0');
			ObjPreviewDivComments.value = sComments;

            //make visible
            if (bReadOnly)
            {
              ObjPreviewDivAccept.disabled = true;
              ObjPreviewDivDecline.disabled = true;
              ObjPreviewDivComments.disabled = 'true';
              ObjPreviewDivSave.disabled = 'true';
            }
	
			objPreviewDiv.style.visibility = 'visible';
		} else {
			objPreviewDiv.style.visibility = 'hidden';
		}
	}
}
function thumb_preview_doreview_toggle()
{
	//get objects
	var objPreviewDiv = document.getElementById("previewforreview");

	if (objPreviewDiv.className == "")
	{
		objPreviewDiv.className = "locked";
	} else {
		objPreviewDiv.className = "";
	}

}

function getImgObj(strDiv, picid)
{
	var objthumbs = document.getElementById(strDiv);
	var arThumbs = objthumbs.getElementsByTagName('img');
    for (var i = 0; i < arThumbs.length; i++)
	  if (arThumbs[i].getAttribute('mid') == picid)
		return arThumbs[i];
	return null;
}


function addTag($imgId,$strTag)
{
	var objPreviewDivImgId = document.getElementById($imgId);
	//Get the tag value and id from object names
	var objPreviewDivImgTag = document.getElementById($strTag);
	$the_imageId = objPreviewDivImgId.value;
	$the_imageTag = objPreviewDivImgTag.value;

	//var $valPreviewDivImgTag = document.getElementById($strTag).value;

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	//alert("db_addTag.php?i=" + $valPreviewDivImgId + "&t=" + $valPreviewDivImgTag + "&ms=" + now.getTime());

	httpSEND.open("GET", "/db_addTag.php?i=" + $the_imageId + "&t=" + $the_imageTag + "&ms=" + now.getTime(), true);

	httpSEND.onreadystatechange = function ()
	{
		if (httpSEND.readyState == 4) 
		{
			var objPreviewDivTags = document.getElementById("preview_tags");
			var bEditTags = (document.getElementById("preview_newtag") != null);
			var tags = httpSEND.responseText;
			objPreviewDivTags.innerHTML = tagsHTML(tags, bEditTags);
			getImgObj("thumbs", $the_imageId).setAttribute('tags', tags);
		}
	}

 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

	//clear the inputbox in the DIV
	objPreviewDivImgTag.value = '';
}

function delTag($imgId,$tagList)
{
	//Get the tag value and id from object names
	var objPreviewDivImgId = document.getElementById($imgId);
	var objPreviewDivTagListId= document.getElementById($tagList);
	$the_imageId = objPreviewDivImgId.value;
	$the_imageTag = objPreviewDivTagListId.options[objPreviewDivTagListId.selectedIndex].text;

	//var $valPreviewDivImgTag = document.getElementById($strTag).value;

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	//alert("db_delTag.php?i=" + $valPreviewDivImgId + "&t=" + $valPreviewDivImgTag + "&ms=" + now.getTime());
	httpSEND.open("GET", "/db_delTag.php?i=" + $the_imageId + "&t=" + $the_imageTag + "&ms=" + now.getTime(), true);
	httpSEND.onreadystatechange = function ()
	{
		if (httpSEND.readyState == 4) 
		{
			var objPreviewDivTags = document.getElementById("preview_tags");
			var bEditTags = (document.getElementById("preview_newtag") != null);
			var tags = httpSEND.responseText;
			objPreviewDivTags.innerHTML = tagsHTML(tags, bEditTags);
			getImgObj("thumbs", $the_imageId).setAttribute('tags', tags);
		}
	}

 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}

function pop_thumbs(callingPage,ppp,strObjThumbsDiv,strObjTagSearch,bStrict,bEnablePopup,src,srcOptions, nPage, fDone)
{
	//AJAX retrive thumbs (using options) then js inject into document div
	//ppp = pics per page. limit output to this number
	//strTags is name of tagsearch box
	//bStrict = Type of tag search - 1:strict; 0:rough; -1:sparse

	//Can use different soruces of thumbs:
	//	src
	//		-1 = non-Live
	//		 0 = Live DB
	//		 1 = Review
	//		 2 = Live DB no rated media
	//	srcOptions
	//		 = which review set to pick from

	//callingPage (use different MAX values depending which page called)
	//	1 = Reviews
	//	2 = LiveNonLive page
	//	3 = DoReview page
	//	4 = Reviews (disable drag drop)
	//	'' = gallery
	//
	//  nPage -1 or page number, 0 to n

	var httpREAD = getHTTPObject();
	var objThumbsDiv = document.getElementById(strObjThumbsDiv);
	$strTags = "";

	if (strObjTagSearch) 
	{
		var objTagSearch = document.getElementById(strObjTagSearch);
		$strTags = objTagSearch.value;
	}

	//set the correct url 
	$strURL = "/db_getThumbs.php?";

	var now = new Date();
	
	if ($strTags)
	{
		$strTags = $strTags.replace(/[\s]+/g, ",");

		$strURL = $strURL + "t="+$strTags+"&";
		$strURL = $strURL + "s="+bStrict+"&";
	}
	//use calling page, so thumbs.php knows which javascript to use in each returned image
	$strURL = $strURL + "cp="+callingPage+"&";

	$strURL = $strURL + "src="+src+"&";
    if (srcOptions == null)
        srcOptions = '';
	$strURL = $strURL + "so="+srcOptions+"&";

	if (callingPage=='3')
	{
		//if doing a review, need to add the rdsid so getthumbs.php can use it too!
		$rdsid = document.getElementById('currRevDataSetId').value;
		$strURL = $strURL + "so2="+$rdsid+"&";
	}

	//if gallery - then supply username info!
	//this then supplies username, but starts with a _ for catching here
	if (callingPage.charAt(0)=='_')
	{
		$strURL = $strURL + "h="+callingPage.substring(1)+"&";
	}

	$strURL = $strURL + "p="+bEnablePopup+"&";
	if (nPage != null)
	{
		$strURL  +=  "pg="+nPage+"&";
		$strURL  +=  "ppp="+ppp+"&";
	}
	$strURL = $strURL + "ms=" + now.getTime();


	//alert($strURL);

	//retrieve the thumbnails (default settings - view all)
	httpREAD.open("GET", $strURL, true);

	// handle what to do with the feedback (inject thumbs div)
	httpREAD.onreadystatechange = function () {
		if (httpREAD.readyState == 4) 
  		{
			//alert('yeah');
			var sResult = httpREAD.responseText;
			//alert(sResult);
			if (fDone && typeof fDone == 'function')
			{
				fDone(sResult);
			}

			//update drag drop code IF this is the last group of divs
			if (callingPage=='1')
			{
				reviewThumbsCount = reviewThumbsCount + 1;
				//$thisCounter = reviewThumbsCount;
				//$thisCounterMax = reviewThumbsCountMax;
				if (reviewThumbsCount == reviewThumbsCountMax)
				{
					runLoadImageDragDropCode('1');
				}
			}
			if (callingPage=='2')
			{
				liveNonLiveThumbsCount = liveNonLiveThumbsCount + 1;
				//$thisCounter = liveNonLiveThumbsCount;
				//$thisCounterMax = liveNonLiveThumbsCountMax;
				if (liveNonLiveThumbsCount == liveNonLiveThumbsCountMax)
				{
					runLoadImageDragDropCode('2');
				}
			}
		}
	}
 	
	httpREAD.send(null);
}

function update_pop_thumbs($reviewId,$allowDragDrop)
{
	//Update all Divs.
	//ALL IMAGE should be last, as after that is run, drag-drop elements are then tagged

	reviewThumbsCount = 0;
	callingpagething = '';
	if ($allowDragDrop=="1")
	{
		callingpagething = '1';
	}
	//pop images for THIS REVIEW (1) Disable Popups (0),
	pop_thumbs(callingpagething,'10','rev_thisimage','','','0','1',$reviewId, null, mkShowIconsClosure('rev_thisimage'));

	//pop images for NONLIVE IMAGES (-1) Disable Popups (0),
	pop_thumbs(callingpagething,'10','rev_newimage','','','0','-1','', null, mkShowIconsClosure('rev_newimage'));
}

function mkShowIconsClosure(strTargetDiv)
{
	f = function(strHTML)
	{
		var objThumbsDiv = document.getElementById(strTargetDiv);
		objThumbsDiv.innerHTML = strHTML;
	}
	return f;
}

function update_pop_thumbs_lnl()
{
	//Update 2 DIVS for Live/NonLive page
	//ALL IMAGE should be last, as after that is run, drag-drop elements are then tagged
	var objReviewID = document.getElementById('currRevIdOnPage');
	rid = objReviewID.value;

	liveNonLiveThumbsCount = 0;

	//pop images for NONLIVE IMAGES (-1) Disable Popups (0),
	pop_thumbs('2','10','media_nonlive','','','0','-1',rid, null, mkShowIconsClosure('media_nonlive'));

	//pop images for ALL IMAGES (0) Disable Popups (0),
	pop_thumbs('2','10','media_live','','','0','0',rid, null, mkShowIconsClosure('media_live'));
}

function update_pop_thumbs_dorev($reviewId,$enablePopups)
{
	//Update 1 DIVS for doing Review

	doReviewThumbsCount = 0;

	//pop images for THIS REVIEW (1) Disable Popups,
	pop_thumbs('3','10','doReviewImageContainer','','',$enablePopups,'1',$reviewId, null, mkShowIconsClosure('doReviewImageContainer'));
}



function addReview($strRevName)
{
	//Get the tag value and id from object names
	var objReviewTxt = document.getElementById($strRevName);
	
	var revName = objReviewTxt.value;
    if (revName == '')
    {
        alert('Please enter a review name');
        return;
    }

	//alert($revName);
	
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	httpSEND.open("GET", "/db_addReview.php?n=" + revName + "&ms=" + now.getTime(), true);

	// don't need to handle any returned value - assume ok. 
	// handle what to do with the feedback (Just refresh the DIVS)
/*	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
				var sResult = httpSEND.responseText;
				alert("result="+sResult);
		}
	}
*/
 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

	//clear the inputbox
	objReviewTxt.value = '';

	location.reload();
}

function setMyReviewStatus($rid,$status,$userdbid)
{
	//Set Review status (personal level)

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	httpSEND.open("GET", "/db_setMyReviewStatus.php?rid=" + $rid + "&status=" + $status + "&uid=" + $userdbid + "&ms=" + now.getTime(), true);

	//alert('rah');

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			//alert('rah2');
			location.reload();
		}
	}

 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}
function setAdminReviewStatus($rid,$status)
{
	//Set Review status (whole admin level)

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	httpSEND.open("GET", "/db_setAdminReviewStatus.php?rid=" + $rid + "&status=" + $status + "&ms=" + now.getTime(), true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			location.reload();
		}
	}


 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}

function setAdminUserStatus($uid,$status)
{
	//Set User status

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	httpSEND.open("GET", "/db_setAdminUserStatus.php?uid=" + $uid + "&status=" + $status + "&ms=" + now.getTime(), true);

	// handle what to do with the feedback
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			location.reload();
		}
	}


 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}

function setUserAuthority($uid,$authority,$set)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	httpSEND.open("GET", "/db_setUserAuthority.php?uid=" + $uid + "&authority=" + $authority + "&set=" + $set + "&ms=" + now.getTime(), true);
	// handle what to do with the feedback
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			location.reload();
		}
	}


 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}

var dragDropObj = null;

function runLoadImageDragDropCode(callingPage)
{
	//grab imgs from all 3 DIVS and make them dragable, etc.
	//Must do this in one go so that there is only one dragDropObj thus each object has its own...
	//	... unique dragdropid

	//also cant just output the code after the images, as it wont run
	//so must do this after all images dynamically created, then run this function to make them draggable

	//alert(callingPage);

	if (callingPage=='1')
	{
		$useDiv1 = 'rev_thisimage';
		$useDiv2 = 'rev_newimage';
//		$useDiv3 = 'rev_allimage';
//		$useDivTrash = 'img_trash';
		$useDivTrash = 'rev_newimage';
	}
	if (callingPage=='2')
	{
		$useDiv1 = 'media_nonlive';
		$useDiv2 = 'media_live';
	}
	//if (callingPage=='3')
	//{
	//	$useDiv1 = 'doReviewImageContainer';
	//}

	dragDropObj = new DHTMLgoodies_dragDrop();

	
	//alert($useDiv1+' '+document.getElementById('rev_thisimage'));

	//------------------------ First Div
	var objCallingDiv = document.getElementById($useDiv1);
	var objsImg = objCallingDiv.getElementsByTagName("img"); 
	for (var i = 0; i < objsImg.length; i++) {
		idImg = objsImg[i].getAttribute("id"); 
		//alert(i+'/'+objsImg.length+' '+idImg+' '+objsImg.src);

		dragDropObj.addSource(idImg,true);
	}


	if (callingPage=='1' || callingPage=='2') {	
	//------------------------ Second Div
	var objCallingDiv = document.getElementById($useDiv2);
	var objsImg = objCallingDiv.getElementsByTagName("img"); 
	for (var i = 0; i < objsImg.length; i++) { 
		idImg = objsImg[i].getAttribute("id"); 
		//alert(i+'/'+objsImg.length+' '+idImg+' '+objsImg.src);

		dragDropObj.addSource(idImg,true);
	}
	}

	if (0 && callingPage=='1') {
	//------------------------ Third Div
	var objCallingDiv = document.getElementById($useDiv3);
	var objsImg = objCallingDiv.getElementsByTagName("img"); 
	for (var i = 0; i < objsImg.length; i++) { 
		idImg = objsImg[i].getAttribute("id"); 
		//alert(i+'/'+objsImg.length+' '+idImg+' '+objsImg.src);

		dragDropObj.addSource(idImg,true);
	}
	//------------------------
	}


	// Set drop targets. Call function on drop	
	if (callingPage=='1')
	{
		//reviews
		dragDropObj.addTarget($useDiv1,'edReviewDropImage');		//Div this Review
		dragDropObj.addTarget($useDivTrash,'edReviewDropImageTrash');	//Trash
	}
	if (callingPage=='2')
	{
		//live non-live
		dragDropObj.addTarget($useDiv1,'edMediaMakeNonLive');	//Div Live
		dragDropObj.addTarget($useDiv2,'edMediaMakeLive');	//Div NonLive
	}
	//if (callingPage=='3')
	//{
	//	//live non-live
	//	dragDropObj.addTarget($useDiv1,'edMediaMakeNonLive');	//Div Live
	//	dragDropObj.addTarget($useDiv2,'edMediaMakeLive');	//Div NonLive
	//}

	//Init now
	dragDropObj.init();
}

function edReviewDropImage(idOfDraggedItem,targetId,x,y)
{
	//assume only IMG objects are ever passed to here
	//therefore can reference IMG tags


	if (idOfDraggedItem.toLowerCase().indexOf('rev') == -1)
	{
		//Disallow picking up a review image and dropping it in review again

		var html = "";
		html = 'Add to Review: ItemId:"' + idOfDraggedItem + '"\n';
		html = html + ' MediaId:'+document.getElementById(idOfDraggedItem).getAttribute('mid')+' dropped into '+targetId+'\n';
		html = html + ' ReviewID:'+document.getElementById('currRevIdOnPage').value;
		//alert(html);

		//Add
		addDelMediaToReview('1',document.getElementById('currRevIdOnPage').value,document.getElementById(idOfDraggedItem).getAttribute('mid'));
	}

}
function edReviewDropImageTrash(idOfDraggedItem,targetId,x,y)
{
	//assume only IMG objects are ever passed to here
	//therefore can reference IMG tags
	if (idOfDraggedItem.toLowerCase().indexOf('live') == -1)
	{
		//Disallow trying to Trash one of the live elements

		var html = "";
		html = 'TRASH: ItemId:"' + idOfDraggedItem + '"\n';
		html = html + ' MediaId:'+document.getElementById(idOfDraggedItem).getAttribute('mid')+' dropped into '+targetId+'\n';
		html = html + ' ReviewID:'+document.getElementById('currRevIdOnPage').value;		
		//alert(html);

		//Del
		addDelMediaToReview('0',document.getElementById('currRevIdOnPage').value,document.getElementById(idOfDraggedItem).getAttribute('mid'));
	}
}

function edMediaMakeNonLive(idOfDraggedItem,targetId,x,y)
{
	//assume only IMG objects are ever passed to here
	//therefore can reference IMG tags

	if (idOfDraggedItem.toLowerCase().indexOf('nonlive') == -1)
	{
		//Disallow picking up a nonlive and dropping it in nonlive

		var html = "";
		html = 'Make NonLive: ItemId:"' + idOfDraggedItem + '"\n';
		html = html + ' MediaId:'+document.getElementById(idOfDraggedItem).getAttribute('mid')+' dropped into '+targetId+'\n';
		html = html + ' ReviewID:'+document.getElementById('currRevIdOnPage').value;
		//alert(html);

		//Make Non Live
		liveNonLiveMedia('0',document.getElementById(idOfDraggedItem).getAttribute('mid'));
	}
}
function edMediaMakeLive(idOfDraggedItem,targetId,x,y)
{
	//assume only IMG objects are ever passed to here
	//therefore can reference IMG tags

	if (idOfDraggedItem.toLowerCase().indexOf('nonlive') != -1)
	{
		//Disallow picking up a live and dropping it in live

		var html = "";
		html = 'Make Live: ItemId:"' + idOfDraggedItem + '"\n';
		html = html + ' MediaId:'+document.getElementById(idOfDraggedItem).getAttribute('mid')+' dropped into '+targetId+'\n';
		html = html + ' ReviewID:'+document.getElementById('currRevIdOnPage').value;
		//alert(html);

		//Make Live
		liveNonLiveMedia('1',document.getElementById(idOfDraggedItem).getAttribute('mid'));
	}
}



function addDelMediaToReview($addOrDel,$rid,$mid)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	if ($addOrDel == '1')
	{
		//Add
		html = html + '/db_addMediaToReview.php';
	} else {
		//Del
		html = html + '/db_delMediaFromReview.php';
	}
	html = html + "?rid=" + $rid + "&mid=" + $mid + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			update_pop_thumbs($rid, '1');
            //reviewThumbsCount = 1;            
        	//pop_thumbs('1','10','rev_thisimage','','','0','1',$rid);
            //runLoadImageDragDropCode(1);
        }
	}

 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

}
function liveNonLiveMedia($liveOrNonLive,$mid)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	if ($liveOrNonLive == '1')
	{
		//Make Live
		html = html + '/db_makeMediaLive.php';
	} else {
		//Make nonLive
		html = html + '/db_makeMediaNonLive.php';
	}
	html = html + "?mid=" + $mid + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			update_pop_thumbs_lnl();
		}
	}

 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

}

function saveDoReviewMediaItem($strrdsid,$strmid,$strrdoaccept,$strcomments)
{
	//pass the object NAMES to this function, to interrogate.
	var objRdsId = document.getElementById($strrdsid);
	var objMId = document.getElementById($strmid);
	var objRdoAccept = document.getElementById($strrdoaccept);
	var objComm = document.getElementById($strcomments);

	//now get values
	$rdsid = objRdsId.value;
	$mid = objMId.value;
	$accept = objRdoAccept.checked;
	$comm = objComm.value;

	//alert('|'+$rdsid+'|'+$mid+'|'+$accept+'|'+$comm+'|');

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_addReviewResultsToDataset.php";
	html = html + "?rdsid=" + $rdsid;
	html = html + "&rmid=" + $mid;
	if ($accept) 
	{
		html = html + "&accept=1";
	} else {
		html = html + "&accept=0";
	}
	//comments need spaces taken out? - try without for now
	$strComm = $comm;
	html = html + "&c=" + $strComm;
	html = html + "&ms=" + now.getTime();

	httpSEND.open("GET", html, true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			//	var sResult = httpSEND.responseText;
            accept = ($accept) ? '1' : '0';
            objImg = getImgObj("doReviewImageContainer", $mid);
   			objImg.setAttribute('accept', accept);
   			objImg.setAttribute('comments', $strComm);
            thumb_preview_doreview_toggle();
		}
	}

 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}

function reg_sendActCodeToEmail(strObjEmail,strObjDbEmail,strObjEmailInnerDiv) 
{
	//get object
	var objEmail = document.getElementById(strObjEmail);
	var objDbEmail = document.getElementById(strObjDbEmail);
	var objEmailInnerDiv = document.getElementById(strObjEmailInnerDiv);

	if (checkMail(objEmail.value)) {
		//Accept Email ad

		//Set hidden field to accept email address
		objDbEmail.value = objEmail.value;

		//change the div contents from input box to text?

		//Send Email to address and check t make sure email doesnt already exist
		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		var html = "";
		html = html + "/db_registerUserSendEmail.php";
		html = html + "?email=" + objDbEmail.value;
		html = html + "&ms=" + now.getTime();
		//alert(html);

		httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () {
			if (httpSEND.readyState == 4) 
 				{
				//read result - 0 1 2?
				var sResult = httpSEND.responseText;
				//alert("result="+sResult);

				if (sResult=="2") {
					//email exist AND auth!
					objEmailInnerDiv.innerHTML = "Email address : "+objDbEmail.value+"<br><font color='#FF0000'>Email address already exists in the DB and Account is already Activated.</font><br><br>\nIf this is your account [ <a href='/'>Please Login</a> ]<br><br>\nIf this is <u>not</u> your account [ <a href='/register.php'>Try again</a> ]";

				}

				if (sResult=="1") {
					//email sent
					objEmailInnerDiv.innerHTML = "Email address : "+objDbEmail.value+"<br><font color='#008800'>Email Sent.<br><br>Please enter the Activation Code when you receive the email.</font>";
					document.getElementById('section2').style.display = 'inline';	//display
					Rounded("div#register2","#FFFFFF","#ECECFF");
				}

				if (sResult=="0") {
					//email exist - but not auth
					objEmailInnerDiv.innerHTML = "Email address : "+objDbEmail.value+"<br><font color='#FF0000'>Email address already exists in the DB - but Account isn't activated yet.</font><br><br>\nIf this is <u>not</u> your account [ <a href='/register.php'>Try again</a> ]</font>";
					document.getElementById('section2').style.display = 'inline';	//display
					Rounded("div#register2","#FFFFFF","#ECECFF");

				}

				if (sResult=="9") {
					alert('Email sending failed');
					document.getElementById('section2').style.display = 'inline';	//display
					Rounded("div#register2","#FFFFFF","#ECECFF");
				}	
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);
		
	} else {
		//Reject Email ad
		alert("Please enter a valid email address.");
	}
}

function reg_checkActCode(strObjActCode,strObjDbEmail,strObjActInnerDiv) 
{
	//get objects
	var objDbEmail = document.getElementById(strObjDbEmail);
	var objActCode = document.getElementById(strObjActCode);
	var objActInnerDiv = document.getElementById(strObjActInnerDiv);

	//alert("Email:"+objDbEmail.value+"\nCode:"+objActCode.value);

	//check auth code and email match
	//if auth <>0 then account already activated

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_registerUserCheckActivationCode.php";
	html = html + "?email=" + objDbEmail.value;
	html = html + "&act=" + objActCode.value;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	//make sure thing is shown!
	document.getElementById('section2').style.display = 'inline';	//display

	httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
 			{
				//read result - 0 1 2?
				var sResult = httpSEND.responseText;
				//alert("result="+sResult);
				if (sResult=="1") 
				{
					//Yep all ok! Proceed!
					objActInnerDiv.innerHTML = "<font color='#CCFFCC'>All cool - lets roll!</font>";
					document.getElementById('section1').style.display = 'none';	//hide
					document.getElementById('section2').style.display = 'none';	//hide
					document.getElementById('section3').style.display = 'inline';	//display
					Rounded("div#register3","#FFFFFF","#ECECFF");
					Rounded("div#register3b","#FFFFFF","#ECECFF");

					reg_getCurrUID('dbEmail','currUID','newUIDSetValue');
					reg_checkIfNeedInputNewUID('dbEmail','RowNewUID','RowCurrUID');

				}
				if (sResult=="0") 
				{
					//Newp - bad combo (even email may not exist)
					objActInnerDiv.innerHTML = "<font color='#FF0000'>Email / Activation Code mismatch!</font>";
				}
				if (sResult=="2") 
				{
					//Newp - account already activated
					objActInnerDiv.innerHTML = "<font color='#FF0000'>Account has already been activated.</font><br><br>\nIf this is your account [ <a href='/'>Please Login</a> ]";
				}

	
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);

}

function reg_checkNewUid(strObjNewUID,strObjNewUIDSetValue,strObjNewUIDMsg,strObjDbEmail)
{
	//get objects
	var objNewUID = document.getElementById(strObjNewUID);
	var objNewUIDSetValue = document.getElementById(strObjNewUIDSetValue);
	var objNewUIDMsg = document.getElementById(strObjNewUIDMsg);
	var objDbEmail = document.getElementById(strObjDbEmail);

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_registerSetNewUID.php";
	html = html + "?email=" + objDbEmail.value;
	html = html + "&uid=" + objNewUID.value;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
 			{
				//read result - 0 1 2?
				var sResult = httpSEND.responseText;
				//alert("result="+sResult);
				if (sResult=="1") 
				{
					//Yep all ok! UID set
					objNewUIDMsg.innerHTML = "<font color='#008800'>Username '"+objNewUID.value+"' set!</font>";
					objNewUIDSetValue.value = objNewUID.value;
				}
				if (sResult=="0") 
				{
					//Newp - UID Exists
					objNewUIDMsg.innerHTML = "<font color='#FF0000'>Username '"+objNewUID.value+"' already exists.</font>";
					objNewUID.value = "";
				}
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);
}

function reg_getCurrUID(strObjDbEmail,strObjCurrUID,strNewUIDSetValue)
{
	//get objects
	var objCurrUID = document.getElementById(strObjCurrUID);
	var objDbEmail = document.getElementById(strObjDbEmail);
	var objNewSetUID = document.getElementById(strNewUIDSetValue);

	//set to current UID
	objNewSetUID.value = objCurrUID.value;

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_registerGetCurrUID.php";
	html = html + "?email=" + objDbEmail.value;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () 
	{
		if (httpSEND.readyState == 4) 
 		{
			//read result - 0 1 2?
			var sResult = httpSEND.responseText;
			//alert("result="+sResult);

			//Got UID - update "currUID" field
			objCurrUID.innerHTML = "<b>"+sResult+"</b>";

			//set to new UID
			objNewSetUID.value = sResult;
		}
	}

	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}

function reg_checkNewPass(strObjNewPass1,strObjNewPass2,strObjNewUIDSetValue,strObjNewPassMsg,strObjDbEmail)
{
	//get objects
	var objNewPass1 = document.getElementById(strObjNewPass1);
	var objNewPass2 = document.getElementById(strObjNewPass2);
	var objNewUIDSetValue = document.getElementById(strObjNewUIDSetValue);
	var objNewPassMsg = document.getElementById(strObjNewPassMsg);
	var objDbEmail = document.getElementById(strObjDbEmail);

	if (!(objNewPass1.value && objNewPass2.value))
	{
		objNewPassMsg.innerHTML = "<font color='#FF0000'>Please enter your new password twice</font>";

	} else if (objNewPass1.value !== objNewPass2.value) {

		objNewPassMsg.innerHTML = "<font color='#FF0000'>Please retype your password correctly</font>";	

	} else {

		//Both passwords entered and identical

		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		var html = "";
		html = html + "/db_registerSetNewPass.php";
		html = html + "?uid=" + objNewUIDSetValue.value;
		html = html + "&pass=" + objNewPass1.value;
		html = html + "&ms=" + now.getTime();

		//alert(html);

		httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
 			{
				//read result - 0 1 2?
				var sResult = httpSEND.responseText;
				//alert("result="+sResult);
				if (sResult=="1") 
				{
					//Yep all ok! Pass Set
					//objNewPassMsg.innerHTML = "<font color='#008800'>Username and password have been set!</font>";

					document.getElementById('section3').style.display = 'none';	//hide
					document.getElementById('section4').style.display = 'inline';	//display

					//show toolbar to login
					document.getElementById('header_toolbar').style.display='';

				} else {
					//Newp - Error?
					objNewPassMsg.innerHTML = "<font color='#FF0000'>Error...</font>";
				}
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);

	}
}

function reg_checkIfNeedInputNewUID(strObjDbEmail,strObjRowNewUID,strObjRowCurrUID)
{
	//if the email ad and username are the same (in DB) then they need to change the UID.
	//if they are different, assume it has been set.
	
	//This is incase they set username, and then close page. ("UId already exists!", etc)


	//get objects
	var objDbEmail = document.getElementById(strObjDbEmail);
	var ObjRowNewUID = document.getElementById(strObjRowNewUID);
	var ObjRowCurrUID = document.getElementById(strObjRowCurrUID);

	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_registerAreUIDAndEmailSame.php";
	html = html + "?email=" + objDbEmail.value;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () 
	{
		if (httpSEND.readyState == 4) 
			{
			//read result - 0 1 2?
			var sResult = httpSEND.responseText;
			//alert("result="+sResult);
			if (sResult=="1") 
			{
				//Email and UID are the same - so allow user to change UID

				ObjRowCurrUID.style.display = 'none';	//hide
				ObjRowNewUID.style.display = 'inline';	//display



			} else {
				//Not the same - assume they have changed the uid

				ObjRowCurrUID.style.display = 'inline';	//display
				ObjRowNewUID.style.display = 'none';	//hide

			}
		}
	}
	
	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}



function checkMail(strEmailToTest)
{
	var x = strEmailToTest;
	var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(x)) return 1;
	else return 0;
}

function setThumbsContent(strHTML, bShowImage)
{
	var objThumbsDiv = document.getElementById('thumbs');
	objThumbsDiv.innerHTML = (bShowImage) ? "<img class='thumbs_prompt' src='img/search.png' >" : ''; //stop text jumping about
	objThumbsDiv.innerHTML += strHTML;
}

function setThumbsNote(str)
{
	var objNote = document.getElementById('instructions');
	if (str == '' )  str = '&nbsp';
	objNote.innerHTML = str;
}

function setSearchResults(strThumbsHTML)
{
	cursor_clear();
	
	var bGotThumbs = (strThumbsHTML == '') ? false : true;
	if (!bGotThumbs)
	{
		setThumbsContent("<p class='goverlay'><br/><br/>No symbols found,<br>please try another search.</p>", true);
		}
	else
	{
		//strThumbsHTML = '<textarea style="text-align:left;width:100%">'+strThumbsHTML+'</texarea>';
		setThumbsContent(strThumbsHTML, false);
	}
		
	if (!(g_bIsUserLoggedOn && g_bIsAdminUser))
	{
		var strNote = (bGotThumbs)  ? "Move pointer over images to view" 
									+ ((g_bIsUserLoggedOn) ? " and click to \"add to basket\"" : "") + "."
								: '&nbsp';
		setThumbsNote(strNote);
	}
}

function doTagSearch(callinguseridplusunderscore,maxThumbsPerPage,divId,divSearch,page)
{
	//get objects
	objRdo1 = document.getElementById('rdoSearch1');
	objRdo2 = document.getElementById('rdoSearch2');
	objRdo3 = document.getElementById('rdoSearch3');
    var bNoRated = document.getElementById('noRated').checked;
    var search = (bNoRated) ? '2' : '0';

	//work out which search is selected
	var searchType = '';
	if (objRdo1.checked) { searchType = '1'; }
	if (objRdo2.checked) { searchType = '0'; }
	if (objRdo3.checked) { searchType = '-1'; }

	var objPreviewDiv = document.getElementById("preview");
	objPreviewDiv.style.visibility = 'hidden';
	setThumbsContent("<p class='goverlay'><br/><br/>Searching for your symbols...</p>", true);
	cursor_wait();
	pop_thumbs(callinguseridplusunderscore,maxThumbsPerPage,divId,divSearch,searchType,'1',search,'', page, setSearchResults);

}

function clearSearch()
{
	var str = (!g_bIsUserLoggedOn) ?  "<p class='goverlay'>You may search our symbols but to<br/>download them you must login.<br/><br/>"				:  "<p class='goverlay p2'>";
	str +="Click the \"Search\" button to view all symbols,<br/>or look for individual symbols or categories by<br/>typing a word into the Search box first.</p>";
	setThumbsNote('');
	setThumbsContent(str, true);
	var objPreviewDiv = document.getElementById("preview");
	objPreviewDiv.style.visibility = 'hidden';
}


function checkSendContactsEmail(strObjDiv,strObjName,strObjEmail,strObjComments,strObjAllow, strObjSubject)
{
	//get objects
	objDiv = document.getElementById(strObjDiv);
	objName = document.getElementById(strObjName);
	objEmail = document.getElementById(strObjEmail);
	objComments = document.getElementById(strObjComments);
	objAllow = document.getElementById(strObjAllow);
	objSubject = document.getElementById(strObjSubject);

	//alert('rah');

	if ((objName.value.length>0) && (objEmail.value.length>0) && (objComments.value.length>0)) 
	{
		//alert('rah2');
		//send email
		
		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		//need to strip control chars from comments in URL (?& etc)
		var s = objComments.value;
		//allow bit
		var sAllow = "0";
		if (objAllow.checked) { sAllow = "1"; }

        comments = objSubject.value;
        if (comments == null)
            comments = 'General question';
            
		var html = "";
		html = html + "ajax_sendComments.php";
		html = html + "?email=" + encodeURIComponent(objEmail.value);
		html = html + "&name=" + encodeURIComponent(objName.value);
		html = html + "&subject=" + encodeURIComponent(comments);
		html = html + "&comments=" + encodeURIComponent(objComments.value);
		html = html + "&allow=" + sAllow;
		html = html + "&ms=" + now.getTime();

		httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
			{
				//read result - 0 1 2?
				var sResult = httpSEND.responseText;
                if (sResult=="1\n")
                {
					objDiv.innerHTML = "Thank you<br><br>Your comments have been sent.<br><br>Please return to the <a href='/'>Homepage</a>";
                }
				else
                {
					objDiv.innerHTML = "Sorry<br><br>Your comments have NOT been sent.<br><br>Please try again";
                }
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);

	} else {

		alert('Please complete all fields before sending your comments.');

	}
}

function checkSendGroupEmail(strObjCboGroup,strObjBody,useClient)
{
	//get objects
	objCboGroup = document.getElementById(strObjCboGroup);
	objBody = document.getElementById(strObjBody);

	//alert('rah');

	if (objCboGroup.value=="X") 
	{
		alert("Please select a Group");

	} else
    {

		if (useClient || confirm("Do you really want to send this email to the whole group?"))
        {
            //AJAX - get HTTP Object
            var httpSEND = getHTTPObject();
            var now = new Date();

            var uri = "ajax_sendGroupEmail.php";
            var parameters = "group=" + encodeURI(objCboGroup.value);
            parameters += "&body=" + encodeURI(objBody.value);
            parameters +=  "&client=" + encodeURI(useClient);
            parameters +=  "&ms=" + now.getTime();
            
            httpSEND.open("POST", uri, true);
            httpSEND.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            httpSEND.setRequestHeader("Content-length", parameters.length);
            httpSEND.setRequestHeader("Connection", "close");
            httpSEND.send(parameters);

            // handle what to do with the feedback (Just refresh the DIVS)
            httpSEND.onreadystatechange = function () 
            {
                if (httpSEND.readyState == 4) 
                {
                    //read result - 0 1 2?
                    var sResult = httpSEND.responseText;
                    // alert(sResult);
                    if (sResult == '1\n')
                    {
                        objBody.innerHTML = "";
                        objCboGroup.selIndex = 0;
                        alert("Emails sent to Group");
                    }
                    else
                    {   
                        if (useClient)
                        {
                            var s = sResult.split('@@@');
                            var addr = s[1].split(',');
                            var c = 70; // how many emails in each batch
                            for (var i =0; i<addr.length; i+=c)
                            { // we do this other wise too many address break mailto
                                var ad = addr.slice(i, i+c-1).join(',');
                                alert('Email: addresses '+i+' to '+(i+c-1));
                                document.location = s[0]+ad;
                            }
                        }
                        else
                        {
                             alert("At least one email was NOT sent to Group");
                        }
                    }
                }
            }
        
            // indicate that everything has been sent by closing the stream (send null) - no reply otherwise
            //httpSEND.send(null);
        }   
    }        
}

function checkSendGroupHTMLEmail(strObjCboGroup,strObjBody)
{
	
	//get objects
	objCboGroup = document.getElementById(strObjCboGroup);
	//objBody = document.getElementById(strObjBody);
    var editorData = CKEDITOR.instances[strObjBody].getData();

	if (objCboGroup.value=="X") 
	{
		alert("Please select a Group");

	} else
    {

		if (confirm("Do you really want to send this email to the whole group?"))
        {
            //AJAX - get HTTP Object
            var httpSEND = getHTTPObject();
            var now = new Date();

            var uri = "ajax_sendGroupEmail.php";
            var parameters = "group=" + encodeURIComponent(objCboGroup.value);
            parameters +=  "&client=false";
            parameters +=  "&html=true";
            parameters += "&body=" + encodeURIComponent(editorData);
            
            httpSEND.open("POST", uri, true);
            httpSEND.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
            httpSEND.setRequestHeader("Content-length", parameters.length);
            httpSEND.setRequestHeader("Connection", "close");
            httpSEND.send(parameters);

            // handle what to do with the feedback (Just refresh the DIVS)
            httpSEND.onreadystatechange = function () 
            {
                if (httpSEND.readyState == 4) 
                {
                    //read result - 0 1 2?
                    var sResult = httpSEND.responseText;
                    // alert('#'+sResult+'#');
                    if (sResult == '1\n')
                    {
                        //objBody.innerHTML = "";
                        //objCboGroup.selIndex = 0;
                        alert("All emails were sent to Group");
                    }
                    else
                    {
                         alert("At least one email was NOT sent to Group");
                    }
                }
            }
        
            // indicate that everything has been sent by closing the stream (send null) - no reply otherwise
            //httpSEND.send(null);
        }   
    }        
}

function ChangeURLSpecialSymbols(s)
{
	var st = s;
	st = st.replace(/&/g,"***am***");
	st = st.replace(/=/g,"***eq***");
	st = st.replace(/\+/g,"***pl***");
	st = st.replace(/\?/g,"***qu***");
	st = st.replace(/\n/g,"***nl***");

	return st;
}

//---------------------------------

function addNewApp(strObjId,strObjName,strObjBrief,strObjInfo,strObjFeat,strObjSysReq,strObjOther)
{
	//get objects
	objId = document.getElementById(strObjId);

	objName = document.getElementById(strObjName);
	objBrief = document.getElementById(strObjBrief);
	objInfo = document.getElementById(strObjInfo);

	objFeat = document.getElementById(strObjFeat);
	objSysReq = document.getElementById(strObjSysReq);
	objOther = document.getElementById(strObjOther);

	//alert('rah');

	if (!objName.value || !objBrief.value || !objInfo.value) 
	{
		alert("Please complete all fields");

	} else {

		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		//need to strip control chars from comments in URL (?& etc)
		//var s = objInfo.value;
		//s = ChangeURLSpecialSymbols(s);

		//make sure PHP page converts them back again!

		var html = "";
		html = html + "/db_addApp.php";
		html = html + "?id=" + objId.value;
		html = html + "&n=" + objName.value;
		html = html + "&b=" + objBrief.value;
		html = html + "&i=" + ChangeURLSpecialSymbols(objInfo.value);

		html = html + "&f=" + ChangeURLSpecialSymbols(objFeat.value);
		html = html + "&s=" + ChangeURLSpecialSymbols(objSysReq.value);
		html = html + "&o=" + ChangeURLSpecialSymbols(objOther.value);

		html = html + "&ms=" + now.getTime();

		httpSEND.open("GET", html, true);
		//alert(html);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
			{
				//read result - 0 1 2?
				//var sResult = httpSEND.responseText;

				objName.value = "";
				objBrief.value = "";
				objInfo.value = "";

				objFeat.value = "";
				objSysReq.value = "";
				objOther.value = "";

				alert("Information Saved");

				location.reload();

			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);

		

	}	

}

function delApp(strDbIdToDel,strObjId)
{
	//get objects
	//(one of the 2 params will be blank (ie deleting record from Edit, or View all)

	var idToDel = '';
	if (strDbIdToDel=='')
	{
		objId = document.getElementById(strObjId);
		idToDel = objId.value;
	} else {
		idToDel = strDbIdToDel; 
	}

	//---

	
	if (confirm("Are you sure you want to delete this Application?"))
	{
		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		var html = "";
		html = html + "/db_delApp.php";
		html = html + "?id=" + idToDel;
		html = html + "&ms=" + now.getTime();

		httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
			{
				location.reload();
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);
	}	
}

function addNewLic(strObjId,strObjName,strObjBrief)
{
	//get objects
	objId = document.getElementById(strObjId);

	objName = document.getElementById(strObjName);
	objBrief = document.getElementById(strObjBrief);
	
	//alert('rah');

	if (!objName.value || !objBrief.value) 
	{
		alert("Please complete all fields");

	} else {

		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		//need to strip control chars from comments in URL (?& etc)
		//var s = objInfo.value;
		//s = ChangeURLSpecialSymbols(s);

		//make sure PHP page converts them back again!

		var html = "";
		html = html + "/db_addLic.php";
		html = html + "?id=" + objId.value;
		html = html + "&n=" + objName.value;
		html = html + "&b=" + objBrief.value;
		html = html + "&ms=" + now.getTime();

		httpSEND.open("GET", html, true);
		//alert(html);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
			{
				//read result - 0 1 2?
				//var sResult = httpSEND.responseText;

				objName.value = "";
				objBrief.value = "";
				

				alert("Information Saved");

				location.reload();

			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);


	}	

}

function delLic(strDbIdToDel,strObjId)
{
	//get objects
	//(one of the 2 params will be blank (ie deleting record from Edit, or View all)

	var idToDel = '';
	if (strDbIdToDel=='')
	{
		objId = document.getElementById(strObjId);
		idToDel = objId.value;
	} else {
		idToDel = strDbIdToDel; 
	}

	//---

	
	if (confirm("Are you sure you want to delete this License?"))
	{
		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		var html = "";
		html = html + "/db_delLic.php";
		html = html + "?id=" + idToDel;
		html = html + "&ms=" + now.getTime();

		httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
			{
				location.reload();
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);
	}	
}


function addNewSpon(strObjId,strObjName,strObjBrief,strObjUrl)
{
	//get objects
	objId = document.getElementById(strObjId);

	objName = document.getElementById(strObjName);
	objBrief = document.getElementById(strObjBrief);
	objUrl = document.getElementById(strObjUrl);

	//alert('rah');

	if (!objName.value || !objBrief.value) 
	{
		alert("Please complete all fields");

	} else {

		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		//need to strip control chars from comments in URL (?& etc)
		//var s = objInfo.value;
		//s = ChangeURLSpecialSymbols(s);

		//make sure PHP page converts them back again!

		var html = "";
		html = html + "/db_addSpon.php";
		html = html + "?id=" + objId.value;
		html = html + "&n=" + objName.value;
		html = html + "&b=" + objBrief.value;
		html = html + "&u=" + objUrl.value;
		html = html + "&ms=" + now.getTime();

		httpSEND.open("GET", html, true);
		//alert(html);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
			{
				//read result - 0 1 2?
				//var sResult = httpSEND.responseText;

				objName.value = "";
				objBrief.value = "";
				objUrl.value = "";				

				alert("Information Saved");

				location.reload();

			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);
	

	}	

}

function delSpon(strDbIdToDel,strObjId)
{
	//get objects
	//(one of the 2 params will be blank (ie deleting record from Edit, or View all)

	var idToDel = '';
	if (strDbIdToDel=='')
	{
		objId = document.getElementById(strObjId);
		idToDel = objId.value;
	} else {
		idToDel = strDbIdToDel; 
	}

	//---

	
	if (confirm("Are you sure you want to delete this Sponsor?"))
	{
		//AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		var html = "";
		html = html + "/db_delSpon.php";
		html = html + "?id=" + idToDel;
		html = html + "&ms=" + now.getTime();

		httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
			{
				location.reload();
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);
	}	
}

function delUserConfirm(id, name)
{
  if (confirm('Do you want to delete user ' + name + ' ?'))
  { 
    delUser(id);
  }
}

function delUser(idToDel)
{
        //AJAX - get HTTP Object
		var httpSEND = getHTTPObject();
		var now = new Date();

		var html = "";
		html = html + "/db_delUser.php";
		html = html + "?id=" + idToDel;
		html = html + "&ms=" + now.getTime();

		httpSEND.open("GET", html, true);

		// handle what to do with the feedback (Just refresh the DIVS)
		httpSEND.onreadystatechange = function () 
		{
			if (httpSEND.readyState == 4) 
			{
                // make ajax?
				location.reload();
			}
		}
	
		// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
		httpSEND.send(null);
  
}

//---------------------------------

function user_lic_Accept(AcceptReject,LicId,UserDbId)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_userSetAgrLic.php";
	html = html + "?a=" + AcceptReject;
	html = html + "&l=" + LicId;
	html = html + "&u=" + UserDbId;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);
	//location.href = html;

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () 
	{
		if (httpSEND.readyState == 4) 
		{
			//read result - 0 1 2?
			//var sResult = httpSEND.responseText;
			//alert("result="+sResult);
			location.reload();	
		}
	}
	
	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

}

function updateGalleryCartNumItems()
{
	//getobjects
	objTdCartNum = document.getElementById('tdCartItems');
	strCartItems = readCookie('ss_cart_contents');

	//alert(readCookie('ss_cart_contents'));

	//Count Items
	$cartCount = 0;
	if (strCartItems!=null)
	{
		aryCartItems = strCartItems.split('-');
		$cartCount = (aryCartItems.length - 1);
	} 

	objTdCartNum.innerHTML = $cartCount;


}

function addImgToCart(imgId)
{
	//alert("ImgId : "+imgId);

	//Get current cookie
	strCartItems = readCookie('ss_cart_contents');
	if (strCartItems==null) { strCartItems = ""; }

	//check if item exists in Cookie
	alreadyExists = false;
	if (strCartItems!="")
	{
		aryCartItems = strCartItems.split('-');
		for (i=0; i<aryCartItems.length; ++i) {
  			if (aryCartItems[i]==imgId) { alreadyExists = true; }
		} // for
	}	

	//If item already exists in Cookie, do not add
	if (!alreadyExists) 
	{
		//alert("Current Cookie : "+strCartItems+"\nNew Cookie : "+strCartItems+imgId+"-");
		//append id onto end + '-' for 0 days (clear when browser closes)
		createCookie('ss_cart_contents',strCartItems+imgId+"-",0);
	}
}

function emptyCart()
{
	if (confirm("Are you sure you want to empty the Cart?"))
	{
		eraseCookie('ss_cart_contents');

		document.location.reload();
	}
}

function ad_edMedia(strObjMId)
{
	mid = document.getElementById(strObjMId).value;
	location.href='ad_edMedia.php?id='+mid;
}


function media_UpdateDeleted(mid, bdel)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	if (bdel)
	{
		html = html + '/db_makeMediaDeleted.php';
	} else {
		html = html + '/db_makeMediaUndeleted.php';
	}
	html = html + "?mid=" + mid + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			//var sResult = httpSEND.responseText;
			//alert("result="+sResult);
			alert("Media Changed");

			location.reload();	
		}
	}

 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

}

function media_UpdateRated(mid, brate)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	if (brate)
	{
		html = html + '/db_makeMediaRated.php';
	} else {
		html = html + '/db_makeMediaUnrated.php';
	}
	html = html + "?mid=" + mid + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () {
		if (httpSEND.readyState == 4) 
  		{
			//var sResult = httpSEND.responseText;
			//alert("result="+sResult);

			location.reload();	
		}
	}

 	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

}

function media_UpdateLic(mid,lid)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_mediaUpdateLic.php";
	html = html + "?m=" + mid;
	html = html + "&l=" + lid;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);
	//location.href = html;

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () 
	{
		if (httpSEND.readyState == 4) 
		{
			//var sResult = httpSEND.responseText;
			//alert("result="+sResult);
			alert("Media Changed");

			location.reload();	
		}
	}
	
	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

}

function media_UpdateSpon(mid,sid)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_mediaUpdateSpon.php";
	html = html + "?m=" + mid;
	html = html + "&s=" + sid;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);
	//location.href = html;

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () 
	{
		if (httpSEND.readyState == 4) 
		{
			//var sResult = httpSEND.responseText;
			//alert("result="+sResult);
			alert("Media Changed");

			location.reload();	
		}
	}
	
	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);

}

function importSymToDB(strSymName,strLicId,rowid,strNonSpaceChar) {

	//------------------------------
	//add records to DB
	//------------------------------
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();

	var html = "";
	html = html + "/db_importSymToDb.php";
	html = html + "?keyword=" + strSymName;
	html = html + "&lid=" + strLicId;
	html = html + "&nsc=" + strNonSpaceChar;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);
	//location.href = html;

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () 
	{
		if (httpSEND.readyState == 4) 
		{
			var sResult = httpSEND.responseText;
			//alert("result="+sResult);
			if (sResult=='1') 
			{
				document.getElementById('td'+rowid).innerHTML = "DONE";
			} else {
				document.getElementById('td'+rowid).innerHTML = "ERROR<br>"+sResult;
			}

				
		}
	}
	
	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);




}


/* ##########################################################################################
	My AJAX Edit fields
   ########################################################################################## */
function dbUserEdit_updateField(strUID,strField,strNewVal,strObjValInto,strMsgWhenDone)
{
	//AJAX - get HTTP Object
	var httpSEND = getHTTPObject();
	var now = new Date();
	if (strObjValInto) {
		var objValInto = document.getElementById(strObjValInto);
	}


	var html = "";
	html = html + "/db_userUpdateField.php";
	html = html + "?uid=" + strUID;
	html = html + "&field=" + strField;
	html = html + "&val=" + strNewVal;
	html = html + "&ms=" + now.getTime();

	//alert(html);

	httpSEND.open("GET", html, true);

	// handle what to do with the feedback (Just refresh the DIVS)
	httpSEND.onreadystatechange = function () 
	{
		if (httpSEND.readyState == 4) 
			{
			//read result
			//var sResult = httpSEND.responseText;
			alert(strMsgWhenDone);

			if (objValInto)
			{
				objValInto.innerHTML = strNewVal;
			}
		}
	}
	
	// indicate that everything has been sent by closing the stream (send null) - no reply otherwise
	httpSEND.send(null);
}


function dbUserEdit_textInputPrompt(strUID,strField,strObjValInto)
{
	//get object
	objValInto = document.getElementById(strObjValInto);


	//get new val from user
	var strNewVal = prompt('Enter new Value for this field','');
	if (strNewVal!=' ' && strNewVal!=null && strNewVal)
	{
		dbUserEdit_updateField(strUID,strField,strNewVal,strObjValInto,'Value changed');
	}

}

function dbUserEdit_passInputPrompt(strUID,strField,strObjPass1,strObjPass2)
{
	//get object
	//objValInto = document.getElementById(strObjValInto);

	objNewPass1 = document.getElementById(strObjPass1);
	objNewPass2 = document.getElementById(strObjPass2);

	if (!(objNewPass1.value && objNewPass2.value))
	{
		alert('Please enter your new password twice');

	} else if (objNewPass1.value !== objNewPass2.value) {

		alert('Please retype your password correctly');	

	} else {

		dbUserEdit_updateField(strUID,strField,objNewPass1.value,'','Password has been changed');

	}

	objNewPass1.value = "";
	objNewPass2.value = "";

}

/* ##########################################################################################
	Misc AJAX stuff
   ########################################################################################## */

function getHTTPObject()
{
	var xmlhttp;
	/*@cc_on
	@if (@_jscript_version >= 5)
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
  @else
  xmlhttp = false;
  @end @*/
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		try 
		{
			xmlhttp = new XMLHttpRequest();
		} catch (e) {
		xmlhttp = false;
		}
	}
	return xmlhttp;
}

/*
There are browser compatability issues with getElementById, so this function
SHOULD solve those.
*/
function _getElm(aID)
{ 
	var element = null; 

	if (isMozilla || isIE5) 
		element = document.getElementById(ID) 
	else if (isNetscape4) 
		element = document.layers[ID] 
	else if (isIE4) 
		element = document.all[ID]; 

	return element; 

//	return (document.getElementById) ? document.getElementById(aID)
//		: document.all[aID];
} 

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

function cursor_wait() {
  document.body.style.cursor = 'wait';
}

function cursor_clear() {
  document.body.style.cursor = 'default';
}


function setUserLanguage(userdbid, langID)
{
    var httpSEND = getHTTPObject();
    var now = new Date();

    html = "/db_setUserLanguage.php";
    html += "?uid=" + userdbid;
    html += "&langid=" + langID;
    html += "&ms=" + now.getTime();

    httpSEND.open("GET", html, true);
    
    // handle what to do with the feedback
    httpSEND.onreadystatechange = function () 
    {
        if (httpSEND.readyState == 4) 
        {
            sResult = httpSEND.responseText;
       }
    }
    httpSEND.send(null);
    
}

function setStatusTransitionList(elSelect, status)
{
    var httpSEND = getHTTPObject();
    var now = new Date();

    html = "/db_getStatusTransitions.php";
    html += "?status=" + status;
    html += "&ms=" + now.getTime();
 
    httpSEND.open("GET", html, true);
    
    // handle what to do with the feedback
    httpSEND.onreadystatechange = function () 
    {
        function addOpt(elSel, val, text)
        {
            var elOptNew = document.createElement('option');
            elOptNew.value = val;
            elOptNew.text = text;
            try {
                elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
            }
            catch(ex) {
                elSel.add(elOptNew); // IE only
            }
        }
        
        function removeOpts(elSel)
        {
            for (i=elSel.length-1; i>=0; i--)
            {
                elSel.remove(i);
            }
        }

        if (httpSEND.readyState == 4) 
        {
            var sResult = httpSEND.responseText;
            removeOpts(elSelect);
            addOpt(elSelect, 0, '');
            var rows = sResult.split(';'); 
            for (i=0; i<rows.length-1; i++) // why doesn't for in work here?
            {   
                var row = rows[i];
                var st = row.split(',');
                addOpt(elSelect, st[0], st[1]);
            }
       }
    }
    httpSEND.send(null);
   
}

function addEvent(obj, evType, fn, useCapture){
  if (obj.addEventListener){
    obj.addEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
  }
}

function limitTextAreaText(limitField, limitNum)
{
	if (limitField.value.length > limitNum)
    {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}
    
