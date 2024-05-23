let ajax_data_variable = {};
const AjaxDataEvent = new CustomEvent('ajax_data_received');
const AjaxuploadingEvent = new CustomEvent('ajax_uploading');
const AjaxcancelingEvent = new CustomEvent('ajax_cancel');

let windowload = (_=>{
    'use strict';
    let gettitle = (_=>{
        if(document.title == 'main wall')   return 'main';
        else if(document.title == 'manga')  return 'manga';
        else if(document.title == 'media')  return 'media';
        else if(document.title == 'notes')  return 'notes';
        else return 'search';
    })
    // procedure when a returned answer from api is a string, and store the after process result in ajax variable
    let ajax_answer_string = (string=>{
        string = string.split('<|>');
        string.forEach((ele,i,arr)=>{
            arr[i] = ele.split('<n>');
            arr[i].forEach((ele,i,arr)=>{
                arr[i] = ele.split('<,>');
                arr[i].forEach((ele,i,arr)=>{
                    let x = ele.split('<:>');
                    arr[i] = {[x[0]]:x[1]};
                });
            });
            if(arr[i][0][0].type == 'li') {
                ajax_data_variable.sortlist = arr[i][1];
            }
            if(arr[i][0][0].type == 'new') {
                ajax_data_variable.newlist = arr[i][1];
            }
            if(arr[i][0][0].type == 'cards') {
                ajax_data_variable.cards = arr[i];
            }
            if(arr[i][0][0].type == 'reply') {
                ajax_data_variable.reply = arr[i][1];
            }
        });
        // console.log(ajax_data_variable);
        window.dispatchEvent(AjaxDataEvent);
    });
    // add a message box for any ajax error message
    (_=>{
        let ajaxalertcontainer = document.createElement('div');
        ajaxalertcontainer.classList.add('position-fixed');
        ajaxalertcontainer.classList.add('bottom-0');
        ajaxalertcontainer.classList.add('end-0');
        ajaxalertcontainer.classList.add('w-md-25');
        ajaxalertcontainer.id = 'ajaxalertcontainer';
        document.body.appendChild(ajaxalertcontainer);
    })();
    // create variable messaging function
    let createAjaxMessage = (message=>{
        let ajaxalert = document.createElement('div');
        ajaxalert.classList.add('alert');
        ajaxalert.classList.add('alert-danger');
        ajaxalert.classList.add('mb-2');
        ajaxalert.classList.add('d-flex');
        ajaxalert.classList.add('justify-content-between');
        ajaxalert.setAttribute('alert-dismissible',null);
        let ajaxalertmsg = document.createElement('div');
        ajaxalertmsg.innerText = message;
        ajaxalert.appendChild(ajaxalertmsg);
        let ajaxalertbtn = document.createElement('button');
        ajaxalertbtn.setAttribute('type','button');
        ajaxalertbtn.setAttribute('data-bs-dismiss','alert');
        ajaxalertbtn.classList.add('btn-close');
        ajaxalert.appendChild(ajaxalertbtn);
        document.getElementById('ajaxalertcontainer').appendChild(ajaxalert);
    });
    // ajax routien (data to send ,  the function that receive the answer , mode text=false or xml=true)
    let ajax_post = ((url, data, procedure, mode=false, formdata=false)=>{
        let req = false;
        try{
            req = new XMLHttpRequest();
        }
        catch(err){
            try {
                req = new ActiveXObject('Msxml2.XMLHTTP');
            }
            catch(err2) {
                req = false
            }
        }
        if(req === false)   return req;
        req.open('POST',url ,true);
        if(formdata === false) {
            req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        }
        req.setRequestHeader('Content-Length',data.lenth);
        req.setRequestHeader('Connection','close');
        req.onreadystatechange = (_=> {
            if(req.readyState ===  4){
                if(req.status === 200) {
                    if(!mode && req.responseText != null) {
                        procedure(req.responseText);
                    }
                    else if(mode && req.responseXML != null) {
                        procedure(req.responseXML);
                    }
                    else createAjaxMessage("no data receaved");
                }
                else if(req.status === 302) {
                    let redirect = req.getResponseHeader('location');
                    if(redirect) {
                        window.location.href = redirect;
                    }
                }
                else createAjaxMessage("connection not found");
            }
        });
        req.upload.addEventListener('progress',e=>{
            if(e.lengthComputable) {
                window.ajax_upload_ratio = (e.loaded/e.total*100).toFixed(2);
                window.dispatchEvent(AjaxuploadingEvent);
            }
        })
        if(!window.ajaxcancelattached) {
            window.addEventListener('ajax_cancel',e=> {
                e.preventDefault();
                e.stopPropagation();
                req.abort();
                window.ajaxcancelattached = 1;
            });
        }
        req.send(data);
    });
    // create page info object
    let pageinfo = {
        type:'main',
        order:'desc',
        cardnum:0
    };
    window.pageinfo = pageinfo;
    window.ajax_answer_string = ajax_answer_string;
    window.createAjaxMessage  = createAjaxMessage;
    window.ajax_post          = ajax_post;
    window.gettitle          = gettitle;
    ajax_post('api/main.php','',ajax_answer_string);
});
window.addEventListener('load',windowload);
