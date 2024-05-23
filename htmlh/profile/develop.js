let profilepageonload = (_=>{
    'use strict';
    let ajax_data_variable = {};
    const AjaxDataEvent = new CustomEvent('ajax_data_received');
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
            if(arr[i][0][0].type == 'profile') {
                if(arr[i][1]) {
                    ajax_data_variable.name = arr[i][1][0].name;
                    ajax_data_variable.img = arr[i][1][1].img;
                }
            }
        });
        // console.log(ajax_data_variable);
        window.dispatchEvent(AjaxDataEvent);
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
        req.send(data);
    });
    // set global variables
    let changebtnstat = 'edit';
    let username = document.querySelector('#username').value;
    document.querySelector('#changename').addEventListener('click',e=>{
    e.preventDefault();
        if(changebtnstat == 'edit') {
            e.target.innerText = 'cancel';
            document.querySelector('#username').removeAttribute('disabled');
            document.querySelector('#username').focus();
            changebtnstat = 'cancel';
        }
        else if(changebtnstat == 'cancel') {
            e.target.innerText = 'change';
            document.querySelector('#username').setAttribute('disabled','disabled');
            changebtnstat = 'edit';
        }
    });
    document.querySelector('#username').addEventListener('keyup',e=>{
        e.preventDefault();
        if(username != e.target.value) {
            document.querySelector('button[type="submit"]').removeAttribute('disabled');
        }
        else {
            document.querySelector('button[type="submit"]').setAttribute('disabled','disabled');
        }
    });
    let imghandeler = (e=>{
        e.preventDefault();
        if(e.target.files[0] && ["image/gif","image/jpeg","image/jpg","image/png"]
        .includes(e.target.files[0].type)) {
            document.querySelector('button[type="submit"]').removeAttribute('disabled');
        }
        else {
            document.querySelector('button[type="submit"]').setAttribute('disabled','disabled');
        }
    });
    document.querySelector('#userimg').addEventListener('change',imghandeler);
    document.querySelector('form').addEventListener('submit',e=>{
        e.preventDefault();
        e.stopPropagation();
        if(e.target.img.files[0] && ["image/gif","image/jpeg","image/jpg","image/png"]
        .includes(e.target.img.files[0].type)) {
            let formdata = new FormData();
            formdata.append('req','user');
            formdata.append('type','change');
            formdata.append('img','uimg');
            formdata.append('uimg',e.target.img.files[0]);
            ajax_post('api/profile.php',formdata,ajax_answer_string,false,true);
        }
        if(e.target.un.value != username) {
            let formdata = new FormData();
            formdata.append('req','user');
            formdata.append('type','change');
            formdata.append('un',e.target.un.value);
            ajax_post('api/profile.php',formdata,ajax_answer_string,false,true);
        }
    });
    window.addEventListener('ajax_data_received',_=>{
        if(ajax_data_variable.img) {
            document.querySelector('img').src = ajax_data_variable.img;
        }
        if(ajax_data_variable.name) {
            username = ajax_data_variable.name;
            document.querySelector('#username').value = ajax_data_variable.name;
        }
        document.querySelector('#changename').innerText = 'change';
        changebtnstat = 'edit';
        document.querySelector('#username').setAttribute('disabled','disabled');
        document.querySelector('#userimg').replaceWith(document.createRange()
        .createContextualFragment('<input type="file" name="img" id="userimg" class="form-control">'));
        document.querySelector('#userimg').addEventListener('change',imghandeler);
        document.querySelector('button[type="submit"]').setAttribute('disabled','disabled');
    });
    document.querySelector('#deleteform').addEventListener('submit',e=>{
        e.preventDefault();
        e.stopPropagation();
        if(e.target.pwd.value) {
            let formdata = new FormData();
            formdata.append('req','user');
            formdata.append('type','delete');
            formdata.append('pwd',e.target.pwd.value);
            ajax_post('api/profile.php',formdata,ajax_answer_string,false,true);
            e.target.pwd.value = '';
        }
    })
});
window.addEventListener('load',profilepageonload);