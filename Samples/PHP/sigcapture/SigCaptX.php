﻿<!--
/* ************************************************************************** */
  SigCaptX.php
   
  HTML/PHP form served up via Apache to SigCaptX which allows you to capture signatures and 
  saves them to a folder on the server
  
  Copyright © 2020 Wacom. All Rights Reserved.
  
  v1.0
  
/* ************************************************************************** */
-->
<!DOCTYPE html>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<html>
  <head>
      <style>
		table.siginfo
		{ 
		  border:2px solid; border-color: blue; background-color: powderblue; border-spacing: 0px; margin-left: 20px;
		  display: inline-block; padding: 20px 10px;
		}
		table.siginfo td
		{
			padding: 9px 10px;
		}
		table.capture
		{ 
		  border:2px solid; border-color: blue; background-color: powderblue; border-spacing: 0px; margin-left: 20px;
		  display: inline-block; padding: 10px 15px;
		}
		table.capture td
		{
			padding: 10px 20px;
		}
		table.capture input
		{
			height:8mm; width:25mm; background-color:ForestGreen; color:white; border-radius: 5px; border: 1px solid white;
		}
		textarea
		{
			display:inline; margin-left:30px;
			border:2px solid; border-color: blue; padding: 5px 5px;
		}
	</style>
    <title>TestSDKCapture</title>
	<script src="https://cdn.rawgit.com/jquery/jquery/2.1.4/dist/jquery.min.js"></script>
    <script src="wgssSigCaptX.js"></script>
    <script src="base64.js"></script>
    <script type="text/javascript">
    
function Exception(txt) {
  print("Exception: " + txt);
}
function print(txt) {
  var txtDisplay = document.getElementById("txtDisplay");
  if("CLEAR" == txt) 
  {
    txtDisplay.value = "";
  }
  else 
  {
    txtDisplay.value += txt + "\n";
    txtDisplay.scrollTop = txtDisplay.scrollHeight; // scroll to end
  }
}
      
var wgssSignatureSDK = null;
var sigObj = null;
var sigCtl = null;
var dynCapt = null;

function OnLoad(callback)
{
  print("CLEAR");
  restartSession(callback);
}

function restartSession(callback) 
{
  wgssSignatureSDK = null;
  sigObj = null;
  sigCtl = null;
  dynCapt = null;
  var imageBox = document.getElementById("imageBox");
  if(null != imageBox.firstChild)
  {
    imageBox.removeChild(imageBox.firstChild);
  }
  var timeout = setTimeout(timedDetect, 1500);
  // pass the starting service port  number as configured in the registry
  wgssSignatureSDK = new WacomGSS_SignatureSDK(onDetectRunning, 8000);
  
  function timedDetect() 
  {
    if (wgssSignatureSDK.running) 
    {
      print("Signature SDK Service detected.");
      start();
    } 
    else 
    {
      print("Signature SDK Service not detected.");
    }
  }
  
  
  function onDetectRunning()
  {
    if (wgssSignatureSDK.running) 
    {
      print("Signature SDK Service detected.");
      clearTimeout(timeout);
      start();
    }
    else 
    {
      print("Signature SDK Service not detected.");
    }
  }
  
  function start()
  {
    if (wgssSignatureSDK.running) 
    {
      sigCtl = new wgssSignatureSDK.SigCtl(onSigCtlConstructor);
    }
  }
  
  function onSigCtlConstructor(sigCtlV, status)
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status)
    {
      // Set the license
      sigCtl.PutLicence("eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI3YmM5Y2IxYWIxMGE0NmUxODI2N2E5MTJkYTA2ZTI3NiIsImV4cCI6MjE0NzQ4MzY0NywiaWF0IjoxNTYwOTUwMjcyLCJyaWdodHMiOlsiU0lHX1NES19DT1JFIiwiU0lHQ0FQVFhfQUNDRVNTIl0sImRldmljZXMiOlsiV0FDT01fQU5ZIl0sInR5cGUiOiJwcm9kIiwibGljX25hbWUiOiJTaWduYXR1cmUgU0RLIiwid2Fjb21faWQiOiI3YmM5Y2IxYWIxMGE0NmUxODI2N2E5MTJkYTA2ZTI3NiIsImxpY191aWQiOiJiODUyM2ViYi0xOGI3LTQ3OGEtYTlkZS04NDlmZTIyNmIwMDIiLCJhcHBzX3dpbmRvd3MiOltdLCJhcHBzX2lvcyI6W10sImFwcHNfYW5kcm9pZCI6W10sIm1hY2hpbmVfaWRzIjpbXX0.ONy3iYQ7lC6rQhou7rz4iJT_OJ20087gWz7GtCgYX3uNtKjmnEaNuP3QkjgxOK_vgOrTdwzD-nm-ysiTDs2GcPlOdUPErSp_bcX8kFBZVmGLyJtmeInAW6HuSp2-57ngoGFivTH_l1kkQ1KMvzDKHJbRglsPpd4nVHhx9WkvqczXyogldygvl0LRidyPOsS5H2GYmaPiyIp9In6meqeNQ1n9zkxSHo7B11mp_WXJXl0k1pek7py8XYCedCNW5qnLi4UCNlfTd6Mk9qz31arsiWsesPeR9PN121LBJtiPi023yQU8mgb9piw_a-ccciviJuNsEuRDN3sGnqONG3dMSA", onSigCtlPutLicence);
    }
    else
    {
      print("SigCtl constructor error: " + status);
    }
  }

  function onSigCtlPutLicence(sigCtlV, status) {
    if (wgssSignatureSDK.ResponseStatus.OK == status) {
      dynCapt = new wgssSignatureSDK.DynamicCapture(onDynCaptConstructor);
    }
    else {
      print("SigCtl PutLicence error: " + status);
    }
  }


  function onDynCaptConstructor(dynCaptV, status)
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status)
    {
      sigCtl.GetSignature(onGetSignature);
    }
    else
    {
      print("DynCapt constructor error: " + status);
    }
  }
  
  function onGetSignature(sigCtlV, sigObjV, status)
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status)
    {
      sigObj = sigObjV;
      sigCtl.GetProperty("Component_FileVersion", onSigCtlGetProperty);
    }
    else
    {
      print("SigCapt GetSignature error: " + status);
    }
  }
  
  function onSigCtlGetProperty(sigCtlV, property, status)
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status)
    {
      print("DLL: flSigCOM.dll  v" + property.text);
      dynCapt.GetProperty("Component_FileVersion", onDynCaptGetProperty);
    }
    else
    {
      print("SigCtl GetProperty error: " + status);
    }
  }
  
  function onDynCaptGetProperty(dynCaptV, property, status)
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status)
    {
      print("DLL: flSigCapt.dll v" + property.text);
      print("Test application ready.");
      print("Press 'Capture' to capture a signature.");
	  
      if('function' === typeof callback)
      {
        callback();
      }
    }
    else
    {
      print("DynCapt GetProperty error: " + status);
    }
  }
}

function Capture()
{
  if(!wgssSignatureSDK.running || null == dynCapt)
  {
    print("Session error. Restarting the session.");
    restartSession(window.Capture);
    return;
  }
  
  // Get the signatory name and reason for signing from the input fields on the form
  // so they can be passed to the capture function as parameters
  var signatory1 = document.getElementById('signatory_first').value;
  var signatory2 = document.getElementById('signatory_last').value;
  var fullname = signatory1 + ' ' + signatory2;  
  var reason = document.getElementById('reason').value;
		  
  //print("Who: " + fullname + ".  Why: " + reason);
  
  dynCapt.Capture(sigCtl, fullname, reason, null, null, onDynCaptCapture);

  function onDynCaptCapture(dynCaptV, SigObjV, status)
  {
    if(wgssSignatureSDK.ResponseStatus.INVALID_SESSION == status)
    {
      print("Error: invalid session. Restarting the session.");
      restartSession(window.Capture);
    }
    else
    {
      if(wgssSignatureSDK.DynamicCaptureResult.DynCaptOK != status)
      {
        print("Capture returned: " + status);
      }
      switch( status ) 
      {
          case wgssSignatureSDK.DynamicCaptureResult.DynCaptOK:
          sigObj = SigObjV;
          print("Signature captured successfully");
		  
		  // Now generate a bitmap so the signature can be displayed as an image on the form
          var flags = wgssSignatureSDK.RBFlags.RenderOutputPicture |
                      wgssSignatureSDK.RBFlags.RenderColor24BPP;
          var imageBox = document.getElementById("imageBox");
		  // Call RenderBitmap so we can capture the signature image and get the sigText value
          sigObj.RenderBitmap("bmp", imageBox.clientWidth, imageBox.clientHeight, 0.7, 0x00000000, 0x00FFFFFF, flags, 0, 0, onRenderBitmap);
          break;
        case wgssSignatureSDK.DynamicCaptureResult.DynCaptCancel:
          print("Signature capture cancelled");
          break;
        case wgssSignatureSDK.DynamicCaptureResult.DynCaptPadError:
          print("No capture service available");
          break;
        case wgssSignatureSDK.DynamicCaptureResult.DynCaptError:
          print("Tablet Error");
          break;
        case wgssSignatureSDK.DynamicCaptureResult.DynCaptIntegrityKeyInvalid:
          print("The integrity key parameter is invalid (obsolete)");
          break;
        case wgssSignatureSDK.DynamicCaptureResult.DynCaptNotLicensed:
          print("No valid Signature Capture licence found");
          break;
        case wgssSignatureSDK.DynamicCaptureResult.DynCaptAbort:
          print("Error - unable to parse document contents");
          break;
        default: 
          print("Capture Error " + status);
          break;
      }
    }
  }
  
  function onRenderBitmap(sigObjV, bmpObj, status) 
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status) 
    {
	  // Display the image on the HTML form
      var imageBox = document.getElementById("imageBox");
      if(null == imageBox.firstChild)
      {
        imageBox.appendChild(bmpObj.image);
      }
      else
      {
        imageBox.replaceChild(bmpObj.image, imageBox.firstChild);
      }
	  // Now get the sigText value of the signature object
	  sigObjV.GetSigText(onGetSigText);
    } 
    else 
    {
      print("Signature Render Bitmap error: " + status);
    }
  }
  
  /* This function takes the SigText value returned by the callback and places it in the txtSigtext element on the form */
  function onGetSigText(sigObjV, text, status) 
  {
	if(wgssSignatureSDK.ResponseStatus.OK == status)
    {
	  //print("Getting sigtext from hidden field");
	  // First put the SigText into a hidden input field so it can be posted to PHP
	  var txtSigtext = document.getElementById("Sigtext");
	  txtSigtext.value = text;
	  
	  // Now get the model of the tablet from the AdditionalData and assign that to an input field as well
	  sigObjV.GetExtraData("AdditionalData", onGetExtraData);
	}
	else
	{
		print("Signature GetSigText error: " + status);
	}
  }
    
  // Callback function for retrieving the additional data from the sigObj
  function onGetExtraData(sigObjV, extraData, status)
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status)
    {
      // Having retrieved the additional data we just want the section that gives the tablet details
	  sigObj.GetAdditionalData(26, onGetAdditionalData);
	}
  }
  
  // Callback function for handling the tablet information from the additional details
  function onGetAdditionalData(sigObjV, tabletInfo, status)
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status)
    {
	  // Let's set the tablet details to a default value of "Unknown" just in case
	  var tabletID2 = "Unknown";
	  if (tabletInfo.length > 0)
	  {
		  //print("Tablet Info: " + tabletInfo);
		  // If the tablet info contains a semi-colon then split the string up into separate elements
		  // and extract the STU model number
		  var pos = tabletInfo.indexOf(";");
		  if (pos > 0)
		  {
			  var infoArray = tabletInfo.split(";");
			  var tabletID = infoArray[1];
			  var len = tabletID.length;
			  // Remove leading and trailing single quotes
			  tabletID2 = tabletID.slice(1,len-1);
		  }
		  else
		  {
			  tabletID2 = tabletInfo;
		  }
	  }
	  
	  // If the signing device is a DTU then the tablet info will just be "Wacom Tablet" which isn't very helpful
	  // so in this case check to see if the user has entered the model number on the form - if so then use that instead
	  if (tabletID2.toLowerCase() == "wacom tablet")
	  {
		var userTablet = document.getElementById("Tablet").value;
		if (userTablet.length > 0)
		{
			tabletID2 = userTablet;
		}
	  }
	  
	  var txtTablet = document.getElementById("Tablet");
	  txtTablet.value = tabletID2;		
	  //  Now submit the form so that the information gets passed through to PHP and MySQL
	  document.getElementById('submitId').click();
	}
	else
	{
	   print("GetAdditionalData failed with status: " + status);
	}
  }
}

// This function displays general information about the signature currently displayed at the top of the form
function DisplaySignatureDetails()
{
  if(!wgssSignatureSDK.running || null == sigObj)
  {
    print("Session error. Restarting the session." );
    restartSession(window.DisplaySignatureDetails);
    return;
  }
  sigObj.GetIsCaptured(onGetIsCaptured);
  
  function onGetIsCaptured(sigObj, isCaptured, status)
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status) 
    {
      if(!isCaptured)
      {
        print("No signature has been captured yet." );
        return;
      }
      sigObj.GetWho(onGetWho);
    }
    else 
    {
      print("Signature GetWho error: " + status);
      if(wgssSignatureSDK.ResponseStatus.INVALID_SESSION == status)
      {
        print("Session error. Restarting the session.");
        restartSession(window.DisplaySignatureDetails);
      }
    }
  }
  
  function onGetWho(sigObjV, who, status) 
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status) 
    {
      print("  Name:   " + who);
      var tz = wgssSignatureSDK.TimeZone.TimeLocal;
      sigObj.GetWhen(tz, onGetWhen);
    } 
    else 
    {
      print("Signature GetWho error: " + status);
    }
  }
  
  function onGetWhen(sigObjV, when, status) 
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status) 
    {
      print("  Date:   " + when.toString() );
      sigObj.GetWhy(onGetWhy);
    } 
    else 
    {
      print("Signature GetWhen error: " + status);
    }
  }
  
  function onGetWhy(sigObjV, why, status) 
  {
    if(wgssSignatureSDK.ResponseStatus.OK == status) 
    {
      print("  Reason: " + why);
    } 
    else 
    {
      print("Signature GetWhy error: " + status);
    }
  }
  
}

function AboutBox()
{
  if(!wgssSignatureSDK.running || null == sigCtl)
  {
    print("Session error. Restarting the session.");
    restartSession(window.AboutBox);
    return;
  }
  sigCtl.AboutBox(onAboutBox);
  function onAboutBox(sigCtlV, status) 
  {
    if(wgssSignatureSDK.ResponseStatus.OK != status) 
    {
      print("AboutBox error: " + status);
      if(wgssSignatureSDK.ResponseStatus.INVALID_SESSION == status)
      {
        print("Session error. Restarting the session.");
        restartSession(window.AboutBox);
      }
    }
  }
}
    </script>
  </head>
  <body onload="OnLoad()">
    <div style="width:100%">
      <h2>Test Signature Control</h2>
	</div>

	<form method="post" id="signatureForm"> 
	  
	  <table class="siginfo">
		<tr>
			<td>First name:</td>
			<td>
				<input id="signatory_first" name="Firstname" type="text" value="John">
			</td>
	    </tr>
		<tr>
			<td>Surname: </td>
			<td>
				<input id="signatory_last" name="Lastname" type="text" value="Smith">
			</td>
	    </tr>
		<tr>
			<td>Reason: </td>
			<td>
				<input id="reason" name="Reason" type="text" value="Document approval"> 
			</td>
	    </tr>
		<tr>
			<td>Tablet: </td>
			<td>
				<input id="Tablet" name="Tablet" type="text">
			</td>
	    </tr>
	  </table>
	  
	  <table class="capture">
        <tr>
          <td rowspan="3">
			<div id="imageBox" class="boxed" style="height:35mm;width:60mm; border:1px solid #d3d3d3;" ondblclick="DisplaySignatureDetails(sigObj)" title="Double-click a signature to display its details">
            </div>
          </td>
          <td>
            <input type="button" value="Capture" onclick="Capture()"
            title="Starts signature capture" />
          </td>
		</tr>
		<tr>
		  <td>
            <input type="button" value="About" onclick="AboutBox()"
            title="Displays the Help About box" />
          </td>
		<tr>
          <td>
            <input type="button" value="Info" onclick="DisplaySignatureDetails(sigObj)"
            title="Displays the signature details" />
          </td>
		</tr>
		<tr>
			<td>
				<input type="submit" id="submitId" name="submit" hidden />
			</td>
		</tr>
      </table>
	  
      <textarea cols="60" rows="12" id="txtDisplay"></textarea>
	  <br/>
	  <br/>
	  
	  <input id="Sigtext" name="Sigtext" type="text" value="" hidden>
		  
    </form>
	
    <script>

	  $(document).ready(function() {
	  
		 $('#signatureForm').submit(function (e) {
			  e.preventDefault();
			  
			  var formData = $(this).serialize();
			  
			  $.ajax({
					type:  "POST",
					url: "savesig.php",
					cache: false,
					data: formData,
					success: function(data){
						//print("Data: " + data);
						if (data == '1')
						{
							print("Signature saved successfully to server");
						}
						else
						{
							print("Failed to save signature to server - see error message below.");
							print(data);
							var errorMsg = jQuery(data).text();
							print (errorMsg);
						}
					},
					error: function(x,e) {
						if (x.status == 0) {
							print("You are offline");
						}
						else if (x.status == 404) {
							print("URL not found");
						}
						else
						{
							print ("Error code: " + x.responseText);
						}
					}
			  });
			  
		  });
	  });	 
	  </script>
  </body>
</html>
