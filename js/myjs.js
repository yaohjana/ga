//跳到網址

function jumpto(url){
window.location=url;
}
function open_new(url,title){
　window.open(url, title, config='height=600,width=800');
}
//移動物件
var Layer='';
document.onmouseup=moveEnd;
document.onmousemove=moveStart;
var b;
var c;

function Move(Object,event){
	Layer=Object.id;
	if(document.all){
		document.getElementById(Layer).setCapture();
		b=event.x-document.getElementById(Layer).style.pixelLeft;
		c=event.y-document.getElementById(Layer).style.pixelTop;
	}else if(window.captureEvents){
		window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
		b=event.layerX;
		c=event.layerY;
	}
	/**實現滑鼠單擊字條時，字條置上**/
		document.getElementById(Layer).style.zIndex=iLayerMaxNum;
		iLayerMaxNum=iLayerMaxNum+1;
	/********************************/
}
function moveStart(d){
	if(Layer!=''){
		if(document.all){
			document.getElementById(Layer).style.left=event.x-b;
			document.getElementById(Layer).style.top=event.y-c;
		}else if(window.captureEvents){
			document.getElementById(Layer).style.left=(d.clientX-b)+"px";
			document.getElementById(Layer).style.top=(d.clientY-c)+"px";
		}
	}
}
function moveEnd(d){
	if(Layer!=''){
		if(document.all){
			document.getElementById(Layer).releaseCapture();
			Layer='';
		}else if(window.captureEvents){
			window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
			Layer='';
		}
	}
}
//chat處理
function chat_on(){
	sjgn('chat_c.php?act=chat_on','chat_con');
	var obj=bi('message_all');
	obj.style.visibility='visible';
}
function chat_off(){
	bi('message_all').style.visibility='hidden';
	sjgn('chat_c.php?act=chat_off','chat_con');
}
function chat_send(){
sjp('chat_c.php?act=send','message',gfv(bi('message_to')));
bi('msg').value='';
}
//處理sj
var request = null;
function bi(id){
	return document.getElementById(id);
}
//加入編碼以及讀取數值以用於資料讀取
function bii(id){
	document.getElementById(id).value;
	return document.getElementById(id).value;
}
function sjg(url,id){
	return httpRequest('get',url,true,function(){r2id(id);});
}
function sjgn(url,id){
	return httpRequest('get',url,true,function(){r2idn(id);});
}

function r2id(id){
	if(request.readyState==4 && request.status==200){
		bi(id).innerHTML=request.responseText;
	}else{
		bi(id).innerHTML="<span>loading..........</span>";
	}
}
function r2idn(id){
	if(request.readyState==4 && request.status==200){
		bi(id).innerHTML=request.responseText;
	}
}
function sjp(url,id,arg){
	return httpRequest('post',url,true,function(){r2id(id);},arg);
}
function sjpn(url,id,arg){
	return httpRequest('post',url,true,function(){r2idn(id);},arg);
}
function sjf(url,id,form_id){
		sjp(url,id,gfv(bi(form_id)));
}
function sjfn(url,id,form_id){
		sjpn(url,id,gfv(bi(form_id)));
}

		

//從表單取得數值

function gfv(form){
	var str='',ft,fv;
	for (var i=0;i<form.elements.length;i++){
		fv=form.elements[i];
		if (typeof fv.name!='undefined'){
			ft=fv.type.toLowerCase();
			
			switch(ft){
				case 'select-one':
					str+=fv.name+'='+encodeURIComponent(fv.value)+'&';
					break;
				case 'radio':
					if (fv.checked){
						str+=fv.name+'='+encodeURIComponent(fv.value)+'&';
					}
					break;
				case 'checkbox':
					if (fv.checked){
						str+=fv.name+'='+encodeURIComponent(fv.value)+'&';
					}
					break;
				case 'text':
					str+=fv.name+'='+encodeURIComponent(fv.value)+'&';
					break;
				case 'color':
					str+=fv.name+'='+encodeURIComponent(fv.value)+'&';
					break;
				case 'password':
					str+=fv.name+'='+encodeURIComponent(fv.value)+'&';
					break;
				case 'hidden':
					str+=fv.name+'='+encodeURIComponent(fv.value)+'&';
					break;
				case 'textarea':
					str+=fv.name+'='+encodeURIComponent(fv.value)+'&';
					break;
				default:
					break;
			}
		}
	}

	return str.split(/\s/).join("");
}
/*
參數:
	reqType:HTTP請求型態，例如GET或者POST
	url：伺服器端的URL
	asynch：送出同步或者非同步請求
	respHandle：負責處理伺服器端回應的函式名稱
	任何第五個參數(表示為arguments[4])是POST請求預定要傳送的資料*/
function httpRequest(reqType,url,asynch,respHandle){
//Mozilla-based瀏覽器
	if(window.XMLHttpRequest){
		request = new XMLHttpRequest();
	}else if(window.ActiveXObject){
		request = new ActiveXObject("Msxml2.XMLHTTP");
		if(!request){
			request= new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
//假如ActiveXObject也沒有初始化成功
//request可能還是null
	if(request){
			//假如reqType是POST，函式的第五個參數是POST的資料
			if(reqType.toLowerCase()!="post"){
				initReq(reqType,url,asynch,respHandle);
			}else{
				//POST的資料
				var args=arguments[4];
				if(args != null && args.length >0){
					initReq(reqType,url,asynch,respHandle,args);
				}
			}
	}else{
		alert("Your browser does not permit the use of all of this application's features!");
	}
}

/*初始化已經被建構的Request Object */
function initReq(reqType,url,asynch,respHandle){
	try{
		/*指定要處理HTTP回應的函式*/
		request.onreadystatechange=respHandle;
		request.open(reqType,url,asynch);
		//假如reqType是POST，函式的第五個參數是POST的資料
		if(reqType.toLowerCase()=="post"){
			request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
			request.send(arguments[4]);
		}else{
			request.send(null);
		}
	}catch (errv){
		alert("The application cannot contact the server at the moment. Please try again in a few seconds.\n Error detail:"+errv.message);
	}
}
		
