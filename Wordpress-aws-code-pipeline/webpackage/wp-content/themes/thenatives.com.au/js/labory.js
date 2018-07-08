function getXMLHttpRequest() {
	var xRequest=null;
	if (window.XMLHttpRequest) {
		xRequest=new XMLHttpRequest();
	}
	else{ 
		if (typeof ActiveXObject != "undefined"){
			xRequest=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xRequest;
} 
		
function getbyid(getid,getpage,url){
	var xmlhttp=getXMLHttpRequest();
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			 jQuery("#"+getid).html(xmlhttp.responseText);
			 xmlhttp.close;
		}
	}
	url=(url) ? "?"+url+"&rd="+Math.random() : "?rd="+ Math.random();
	xmlhttp.open("GET",getpage+url,true);
	xmlhttp.send();
}

function postbyurl(getid,getpage,url,unload){
	if(!unload) {
        unload = '';
	}
	var xmlhttp=new getXMLHttpRequest();
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			jQuery("#"+getid).hide();
			jQuery("#"+getid).html(xmlhttp.responseText).fadeIn();
			xmlhttp.close;
		}
	}
	params=(url) ? "?&"+url : "?rd="+Math.random();	
	xmlhttp.open("POST",getpage,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(params);															
}

function postall_or(getid,getpage,url){
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function(){
	   if (xmlhttp.readyState==4 && xmlhttp.status==200){			
			jQuery("#"+getid).html(xmlhttp.responseText).fadeIn();
			xmlhttp.close;
		}
	}
	var mang=url.split("||");
	var sopt=mang.length;
	var data = new FormData();
	for(i=0;i<=sopt-1;i++){
		data.append(mang[i], jQuery('#'+mang[i]).val());
	}
	xmlhttp.open("POST",getpage,true);
	xmlhttp.send(data);							
}

function post_json_byurl(target,temp,getpage,url,target_pag,type){
	if(!target_pag) {
        target_pag = "";
	}
	if(!type) {
        type = 1;
	}
	var xmlhttp=new getXMLHttpRequest();
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){		
			
			var rs=JSON.parse(xmlhttp.responseText);
			var arrs=(rs.datas);
			var template = _.template(jQuery(temp).html());
			switch(type) {
				case 2:
					jQuery.each(arrs,function(i,arr){
						jQuery(target).append(template({arr:arr}));
					})
					break;
				case 3:
					jQuery.each(arrs,function(i,arr){
						jQuery(target).prepend(template({arr:arr}));
					})
					break;
				default:
					var str="";
					jQuery.each(arrs,function(i,arr){
						str+=template({arr:arr});
					})
					str=(str)?str:rs.notfound;
					jQuery(target).html(str);
					if(target_pag!=""){
						jQuery(target_pag).html(rs.numpage);
					}
					break
			}
			blockUi.unblock();
			xmlhttp.close;
		}
	}
	params=(url) ? "?&"+url : "?rd="+Math.random();	
	xmlhttp.open("POST",getpage,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(params);
}

function post_json_allor(target,temp,getpage,url,type){
	var xmlhttp=new getXMLHttpRequest();
    if(!type) {
        type = 1;
    }
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){		
			var arrs=JSON.parse(xmlhttp.responseText);
			var template = _.template(jQuery(temp).html());
			switch(type) {
				case 2:
					jQuery.each(arrs,function(i,arr){
						jQuery(target).append(template({arr:arr}));
					})
					break;
				case 3:
					jQuery.each(arrs,function(i,arr){
						jQuery(target).prepend(template({arr:arr}));
					})
					break;
				default:
					var str="";
					jQuery.each(arrs,function(i,arr){
						str+=template({arr:arr});
					})
					jQuery(target).html(str);
					break
			}
			xmlhttp.close;
		}
	}
	var mang=url.split("||");
	var sopt=mang.length;
	params="?";
	for(i=0;i<=sopt-1;i++){
		params=params + "&" + mang[i] + "=" + escape(jQuery('#'+mang[i]).val()) ;
	}
	xmlhttp.open("POST",getpage,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(params);
}