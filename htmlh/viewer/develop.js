let vieweronload = (_=>{
    'use strict';
    let ajax_data_variable = {};
    const AjaxDataEvent = new CustomEvent('ajax_data_received');
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
            if(arr[i][0][0].type != 'reply') {
                if(arr[i][1]) {
                    ajax_data_variable.previous = arr[i][1][0].prev;
                    ajax_data_variable.next = arr[i][1][1].next;
                }
                if(arr[i][2]) {
                    ajax_data_variable.rows = arr[i][2];
                }
            }
            else {
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
        req.send(data);
    });
    let type = document.querySelector('#startuptype').textContent;
    let id   = document.querySelector('#startupid').textContent;
    document.querySelector('#startuptype').remove();
    document.querySelector('#startupid')  .remove();
    ajax_post('api/viewer.php',`req=view&type=${type}&id=${id}`,ajax_answer_string);
    let createrows = (_=>{
        if(ajax_data_variable.rows) {
            Array.from(document.querySelector('#view-ul-listgroup').children).forEach((ele,i)=>{
                if(i != 0) {
                    ele.remove();
                }
            })
            for (let i = 0; i < ajax_data_variable.rows.length; i++) {
                const ele = ajax_data_variable.rows[i];
                const array = ajax_data_variable.rows;
                let libody = `<li class="list-group-item text-bg-dark">
                <div class="container-fluid">
                    <div class="row fs-5 py-2">
                        <div class="col-md-3 border-end fw-bold mb-2 mb-md-0">`;
                if(Number.isNaN(Number(Object.keys(ele)[0]))) {
                    libody += `<div class="">${Object.keys(ele)[0]}</div>`;
                }
                else {
                    libody += `<div class="visually-hidden">${Object.keys(ele)[0]}</div>`;
                }
                libody += `</div>`;;
                if(type == 'notes' && !Number.isNaN(Number(Object.keys(ele)[0]))) {
                    libody += `<div class="col-md-12" id="snippetssec">`;
                    for(let j = i ; j < array.length ; j++) {
                        libody += `<div class="rounded border border-info p-2 mb-2 bg-secondary">
                        ${Object.values(array[j])[0]}
                        <div class="mt-2 mt-md-0 w-100 d-flex justify-content-end align-items-center">
                        <button class="btn btn-info text-capitalize" actiongroup="edit" data-bs-toggle="modal" data-bs-target="#editmodal">edit</button>
                        <div class="vr mx-2"></div>
                        <button class="btn btn-danger text-capitalize" actiongroup="remove">
                        remove <i class="fa-solid fa-trash-can"></i> 
                        </button>
                        </div>
                        </div>`;
                    }
                    // libody += `<button class="d-block mx-auto btn btn-outline-success py-2 px-3" actiongroup="add">create new snippet</button>`;
                    libody += `</div>`;
                    libody += `</div>
                            </div>
                        </div>
                    </li>`;
                    document.querySelector('#view-ul-listgroup').appendChild(document.createRange()
                    .createContextualFragment(libody));
                    break;
                }
                else if(!(Object.keys(ele)[0] == 'localurl' || Object.keys(ele)[0] == 'img' || Object.keys(ele)[0] == 'date' || Object.keys(ele)[0] == 'tags' || (type == 'manga' && Object.keys(ele)[0] == 'name'))) {
                    libody += `<div class="col-md-9 d-sm-flex justify-content-between">
                    <div class="flex-grow-1">`;
                    libody += Object.values(ele)[0];
                    libody += `</div>
                    <div class="mt-2 mt-md-0 flex-grow-1 d-flex justify-content-end align-items-center">
                    <button class="btn btn-outline-info text-capitalize" actiongroup="edit" data-bs-toggle="modal" data-bs-target="#editmodal">edit</button>`;
                    if(!((type == 'notes' && Object.keys(ele)[0] == 'name') ||
                    (type == 'media' && Object.keys(ele)[0] == 'weburl') ||
                    (type == 'manga' && Object.keys(ele)[0] == 'chapter') ||
                    (type == 'manga' && Object.keys(ele)[0] == 'url'))) {
                        libody += `<div class="vr mx-2"></div>
                        <button class="btn btn-outline-danger text-capitalize" actiongroup="remove">
                        remove <i class="fa-solid fa-trash-can"></i>
                        </button>`;
                    }
                    else {
                        libody += `<div class="vr mx-2 invisible"></div>
                        <button class="btn btn-outline-danger text-capitalize invisible">
                        remove <i class="fa-solid fa-trash-can"></i>
                        </button>`;
                    }
                    libody += `</div>
                    </div>`;
                }
                else if(Object.keys(ele)[0] == 'img'){
                    libody += `<div class="col-md-9">
                    <img src="${Object.values(ele)[0]}" alt="no img found" class="imgfram mb-3">`;
                    libody += `<div class="mt-2 mt-md-0 w-100 d-flex justify-content-end align-items-center">
                    <button class="btn btn-outline-info text-capitalize" actiongroup="edit" data-bs-toggle="modal" data-bs-target="#editmodal">edit</button>
                    <div class="vr mx-2"></div>
                    <button class="btn btn-outline-danger text-capitalize" actiongroup="remove">
                    remove <i class="fa-solid fa-trash-can"></i> 
                    </button>
                    </div>
                    </div>`;
                }
                else if(Object.keys(ele)[0] == 'localurl'){
                    libody += `<div class="col-md-9">
                    <div class="position-relative bg-black mb-3 overflow-hidden" id="videoplayer">
                        <video class="w-100" controls>
                            <source src="${Object.values(ele)[0]}">
                            <p>
                                Your browser doesn't support HTML video. Here is a
                                <a href="${Object.values(ele)[0]}">link to the video</a> instead.
                            </p>
                        </video>
                        <div class="position-absolute bottom-150 w-100 bg-dark-50">
                            <div class="">
                                <input type="range" class="form-range" value="0" id="" min="0" max="2000">
                            </div>
                            <div class="d-flex justify-content-between align-items-center px-3">
                                <span class="fs-7" id="vediotime">00:00:00/00:00:00</span>
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="button" class="btn p-2 border-0" id="videoback10"><i class="fa-solid fa-backward-fast"></i></button>
                                    <button type="button" class="btn p-2 border-0" id="videoback5"><i class="fa-solid fa-backward"></i></button>
                                    <button type="button" class="btn p-2 border-0" id="videoplay">
                                        <i class="fa-solid fa-play"></i>
                                        <i class="fa-solid fa-pause visually-hidden"></i>
                                    </button>
                                    <button type="button" class="btn p-2 border-0" id="videoforword5"><i class="fa-solid fa-forward"></i></button>
                                    <button type="button" class="btn p-2 border-0" id="videoforword10"><i class="fa-solid fa-forward-fast"></i></button>
                                </div>
                                <button type="button" class="btn p-2 border-0" id="fullscreanbtn">
                                    <i class="fa-solid fa-expand"></i>
                                    <i class="fa-solid fa-compress visually-hidden"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
                    libody += `<div class="mt-2 mt-md-0 w-100 d-flex justify-content-end align-items-center">
                    <button class="btn btn-outline-info text-capitalize" actiongroup="edit" data-bs-toggle="modal" data-bs-target="#editmodal">edit</button>
                    <div class="vr mx-2"></div>
                    <button class="btn btn-outline-danger text-capitalize" actiongroup="remove">
                    remove <i class="fa-solid fa-trash-can"></i> 
                    </button>
                    </div>
                    </div>`;
                }
                else if(Object.keys(ele)[0] == 'tags') {
                    libody += `<div class="col-md-9 d-sm-flex justify-content-between">
                    <div class="flex-shrink-1">`;
                    let tags = Object.values(ele)[0].replace(/^{/,'').replace(/}$/,'').split('}{');
                    tags.forEach(tag=>{
                        if(tag) {
                            libody += `<div class="d-inline badge text-bg-secondary rounded rounded-pill p-2 mx-1 position-relative">
                            <span>${tag}</span><i class="bg-dark text-danger fa-solid fa-trash-can p-2 ms-2 rounded-circle visually-hidden" actiongroup="removetag"></i>
                            </div>`;
                        }
                    });
                    libody += `</div>
                    <div class="mt-2 mt-md-0 w-100 d-flex justify-content-end align-items-center">
                    <button class="btn btn-outline-info text-capitalize" actiongroup="addtag">add</button>
                    </div>
                    </div>`;
                }
                else {
                    libody += `<div class="col-md-9 d-sm-flex justify-content-between">`;
                    libody += Object.values(ele)[0];
                    libody += `</div>`;
                }
                libody += `</div>
                        </div>
                    </div>
                </li>`;
                document.querySelector('#view-ul-listgroup').appendChild(document.createRange()
                .createContextualFragment(libody));
            }
            // the next foreach commented because i cant skip it when note render snippets so i create 
            // the previous for loop as a clone for it
            // ajax_data_variable.rows.forEach((ele,i,array)=>{
            //     let libody = `<li class="list-group-item text-bg-dark">
            //     <div class="container-fluid">
            //         <div class="row fs-5 py-2">
            //             <div class="col-md-3 border-end fw-bold mb-2 mb-md-0">`;
            //     if(Number.isNaN(Number(Object.keys(ele)[0]))) {
            //         libody += `<div class="">${Object.keys(ele)[0]}</div>`;
            //     }
            //     else {
            //         libody += `<div class="visually-hidden">${Object.keys(ele)[0]}</div>`;
            //     }
            //     libody += `</div>`;;
            //     if(type == 'notes' && !Number.isNaN(Number(Object.keys(ele)[0]))) {
            //         libody += `<div class="col-md-12" id="snippetssec">`;
            //         for(let j = i ; j < array.length ; j++) {
            //             libody += `<div class="rounded border border-info p-2 mb-2 bg-secondary">
            //             ${Object.values(array[j])[0]}
            //             <div class="mt-2 mt-md-0 w-100 d-flex justify-content-end align-items-center">
            //             <button class="btn btn-info text-capitalize" actiongroup="edit" data-bs-toggle="modal" data-bs-target="#editmodal">edit</button>
            //             <div class="vr mx-2"></div>
            //             <button class="btn btn-danger text-capitalize" actiongroup="remove">
            //             remove <i class="fa-solid fa-trash-can"></i> 
            //             </button>
            //             </div>
            //             </div>`;
            //         }
            //         // libody += `<button class="d-block mx-auto btn btn-outline-success py-2 px-3" actiongroup="add">create new snippet</button>`;
            //         libody += `</div>`;
            //         libody += `</div>
            //                 </div>
            //             </div>
            //         </li>`;
            //         document.querySelector('#view-ul-listgroup').appendChild(document.createRange()
            //         .createContextualFragment(libody));
            //         return;
            //     }
            //     else if(!(Object.keys(ele)[0] == 'img' || Object.keys(ele)[0] == 'date' || Object.keys(ele)[0] == 'tags' || (type == 'manga' && Object.keys(ele)[0] == 'name'))) {
            //         libody += `<div class="col-md-9 d-sm-flex justify-content-between">
            //         <div class="flex-shrink-1">`;
            //         libody += Object.values(ele)[0];
            //         libody += `</div>
            //         <div class="mt-2 mt-md-0 w-100 d-flex justify-content-end align-items-center">
            //         <button class="btn btn-outline-info text-capitalize" actiongroup="edit" data-bs-toggle="modal" data-bs-target="#editmodal">edit</button>`;
            //         if(!((type == 'notes' && Object.keys(ele)[0] == 'name') ||
            //         (type == 'media' && Object.keys(ele)[0] == 'weburl') ||
            //         (type == 'manga' && Object.keys(ele)[0] == 'chapter') ||
            //         (type == 'manga' && Object.keys(ele)[0] == 'url'))) {
            //             libody += `<div class="vr mx-2"></div>
            //             <button class="btn btn-outline-danger text-capitalize" actiongroup="remove">
            //             remove <i class="fa-solid fa-trash-can"></i>
            //             </button>`;
            //         }
            //         else {
            //             libody += `<div class="vr mx-2 invisible"></div>
            //             <button class="btn btn-outline-danger text-capitalize invisible">
            //             remove <i class="fa-solid fa-trash-can"></i>
            //             </button>`;
            //         }
            //         libody += `</div>
            //         </div>`;
            //     }
            //     else if(Object.keys(ele)[0] == 'img'){
            //         libody += `<div class="col-md-9 d-sm-flex justify-content-between">
            //         <img src="${Object.values(ele)[0]}" alt="no img found" class="imgfram">`;
            //         libody += `<div class="mt-2 mt-md-0 w-100 d-flex justify-content-end align-items-center">
            //         <button class="btn btn-outline-info text-capitalize" actiongroup="edit" data-bs-toggle="modal" data-bs-target="#editmodal">edit</button>
            //         <div class="vr mx-2"></div>
            //         <button class="btn btn-outline-danger text-capitalize" actiongroup="remove">
            //         remove <i class="fa-solid fa-trash-can"></i> 
            //         </button>
            //         </div>
            //         </div>`;
            //     }
            //     else if(Object.keys(ele)[0] == 'tags') {
            //         libody += `<div class="col-md-9 d-sm-flex justify-content-between">
            //         <div class="flex-shrink-1">`;
            //         let tags = Object.values(ele)[0].replace(/^{/,'').replace(/}$/,'').split('}{');
            //         tags.forEach(tag=>{
            //             if(tag) {
            //                 libody += `<div class="d-inline badge text-bg-secondary rounded rounded-pill p-2 mx-1 position-relative">
            //                 <span>${tag}</span><i class="bg-dark text-danger fa-solid fa-trash-can p-2 ms-2 rounded-circle visually-hidden" actiongroup="removetag"></i>
            //                 </div>`;
            //             }
            //         });
            //         libody += `</div>
            //         <div class="mt-2 mt-md-0 w-100 d-flex justify-content-end align-items-center">
            //         <button class="btn btn-outline-info text-capitalize" actiongroup="addtag">add</button>
            //         </div>
            //         </div>`;
            //     }
            //     else {
            //         libody += `<div class="col-md-9 d-sm-flex justify-content-between">`;
            //         libody += Object.values(ele)[0];
            //         libody += `</div>`;
            //     }
            //     libody += `</div>
            //             </div>
            //         </div>
            //     </li>`;
            //     document.querySelector('#view-ul-listgroup').appendChild(document.createRange()
            //     .createContextualFragment(libody));
            // });
            // add action to vedio
            if(document.querySelector('#videoplayer')) {
                document.querySelector('#videoplayer video').removeAttribute('controls');
                // view and hide controls
                let thetime = Date.now();
                document.querySelector('#videoplayer').addEventListener('mousemove',_=>{
                    document.querySelector('#videoplayer .position-absolute').classList.remove('bottpm-150');
                    document.querySelector('#videoplayer .position-absolute').classList.add('bottom-0');
                    thetime = Date.now();
                    setTimeout(_=>{
                        if(Date.now() > thetime + 3990) {
                            document.querySelector('#videoplayer .position-absolute').classList.remove('bottom-0');
                            document.querySelector('#videoplayer .position-absolute').classList.add('bottpm-150');
                        }
                    },4000);
                });
                // play pause action
                let playpause = (_=>{
                    
                    if(document.querySelector('#videoplayer video').paused) {
                        document.querySelector('#videoplayer .fa-pause').classList.remove('visually-hidden');
                        document.querySelector('#videoplayer .fa-play').classList.add('visually-hidden');
                        document.querySelector('#videoplayer video').play();
                    }
                    else{
                        document.querySelector('#videoplayer .fa-play').classList.remove('visually-hidden');
                        document.querySelector('#videoplayer .fa-pause').classList.add('visually-hidden');
                        document.querySelector('#videoplayer video').pause();
                    }
                });
                document.querySelector('#videoplayer video').addEventListener('click',playpause);
                document.querySelector('#videoplayer #videoplay').addEventListener('click',playpause);
                document.querySelector('#videoplayer').addEventListener('keypress',e=>{
                    if(e.code == 'Space'){
                        e.preventDefault();
                        playpause();
                    }
                    });
                // end stop action
                let endshow = (_=>{
                        document.querySelector('#videoplayer .fa-play').classList.remove('visually-hidden');
                        document.querySelector('#videoplayer .fa-pause').classList.add('visually-hidden');
                        document.querySelector('#videoplayer video').pause();
                        document.querySelector('#videoplayer video').currentTime = 0;
                });
                document.querySelector('#videoplayer video').addEventListener('ended',endshow);
                // move forward action
                let forwardaction = (function(){
                    if(document.querySelector('#videoplayer video').currentTime +this < document.querySelector('#videoplayer video').duration) {
                        document.querySelector('#videoplayer video').currentTime = document.querySelector('#videoplayer video').currentTime + this;
                    }
                    else {
                    document.querySelector('#videoplayer video').currentTime = document.querySelector('#videoplayer video').duration;
                    }
                });
                document.querySelector('#videoplayer #videoforword5').addEventListener('click',forwardaction.bind(5));
                document.querySelector('#videoplayer').addEventListener('keydown',e=>{
                    if(e.key == 'ArrowRight'){
                        e.preventDefault();
                        forwardaction.bind(5)();
                    }
                    });
                // move backeard action
                let backwardaction = (function(){
                    if(document.querySelector('#videoplayer video').currentTime -this > 0) {
                        document.querySelector('#videoplayer video').currentTime = document.querySelector('#videoplayer video').currentTime - this;
                    }
                    else {
                    document.querySelector('#videoplayer video').currentTime = 0;
                    }
                });
                document.querySelector('#videoplayer #videoback5').addEventListener('click',backwardaction.bind(5));
                document.querySelector('#videoplayer').addEventListener('keydown',e=>{
                    if(e.key == 'ArrowLeft'){
                        e.preventDefault();
                        backwardaction.bind(5)();
                    }
                    });
                // move forward fast action
                document.querySelector('#videoplayer #videoforword10').addEventListener('click',forwardaction.bind(10));
                // move backeard fast action
                document.querySelector('#videoplayer #videoback10').addEventListener('click',backwardaction.bind(10));
                // range change action
                let rangebarachionintrval = setInterval(_=>{
                    let rangebar = document.querySelector('#videoplayer input[type="range"]'); 
                    rangebar.value = document.querySelector('#videoplayer video').currentTime/document.querySelector('#videoplayer video').duration*rangebar.getAttribute('max');
                    let timenow = document.querySelector('#videoplayer video').currentTime;
                    let h = Math.floor(timenow/3600).toString().padStart(2,0);
                    let m = Math.floor((timenow-h*3600)/60).toString().padStart(2,0);
                    let s = Math.floor(timenow-h*3600-m*60).toString().padStart(2,0);
                    let resault = h+':'+m+':'+s+'/';
                    let timeend = document.querySelector('#videoplayer video').duration;
                    h = Math.floor(timeend/3600).toString().padStart(2,0);
                    m = Math.floor((timeend-h*3600)/60).toString().padStart(2,0);
                    s = Math.floor(timeend-h*3600-m*60).toString().padStart(2,0);
                    resault += h+':'+m+':'+s;
                    document.querySelector('#videoplayer #vediotime').textContent = resault;
                },200);
                let rangebarachion = (e=>{
                    document.querySelector('#videoplayer video').currentTime =e.target.value/e.target.getAttribute('max')*document.querySelector('#videoplayer video').duration;
                });
                document.querySelector('#videoplayer input[type="range"]').addEventListener('input',rangebarachion);
                // full screen
                let fullscreenaction =(_=>{
                    if(document.fullscreenElement){
                        document.querySelector('#videoplayer #fullscreanbtn .fa-expand').classList.remove('visually-hidden');
                        document.querySelector('#videoplayer #fullscreanbtn .fa-compress').classList.add('visually-hidden');
                        document.exitFullscreen();
                    }
                    else {
                        document.querySelector('#videoplayer #fullscreanbtn .fa-expand').classList.add('visually-hidden');
                        document.querySelector('#videoplayer #fullscreanbtn .fa-compress').classList.remove('visually-hidden');
                        document.querySelector('#videoplayer').requestFullscreen();
                    }
                });
                document.querySelector('#videoplayer').addEventListener('keypress',e=>{
                    if(e.key == 'f'){
                        e.preventDefault();
                        fullscreenaction();
                    }
                });
                document.querySelector('#videoplayer #fullscreanbtn').addEventListener('click',fullscreenaction);
            }
            // add a new snippet button
            if(type == 'notes') {
                document.querySelector('.list-group-item.text-bg-dark:last-of-type .row.fs-5.py-2').
                appendChild(document.createRange().createContextualFragment
                (`<button class="d-block mx-auto btn btn-outline-success mt-2 py-2 px-3" actiongroup="add">create new snippet</button>`));
            }
            // add a delete button to tags
            Array.from(document.querySelectorAll('.d-inline.badge.text-bg-secondary.rounded.rounded-pill.p-2.mx-1.position-relative')).forEach(ele=>{
                ele.addEventListener('mouseover',_=>{
                    ele.lastElementChild.classList.remove('visually-hidden');
                });
                ele.addEventListener('mouseout',_=>{
                    ele.lastElementChild.classList.add('visually-hidden');
                });
                ele.lastElementChild.addEventListener('click',e=>{
                    e.preventDefault();
                    ajax_post('api/viewer.php',`req=remove&type=${type}&id=${id}&key=${'tag-'+ele.firstElementChild.textContent}`
                    ,ajax_answer_string);
                });
            });
            // add events to button
            Array.from(document.querySelectorAll('[actiongroup]')).forEach(actionbutton=>{
                // remove buttons
                if(actionbutton.getAttribute('actiongroup') == 'remove') {
                    if(actionbutton.parentElement.parentElement.parentElement.id != 'snippetssec') {
                        actionbutton.addEventListener('click',e=>{
                            e.preventDefault();
                            ajax_post('api/viewer.php',`req=${actionbutton.getAttribute('actiongroup')}&type=${type}&id=${id}&key=${actionbutton.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent}`
                            ,ajax_answer_string);
                        });
                    }
                    else {
                        actionbutton.addEventListener('click',e=>{
                            e.preventDefault();
                            ajax_post('api/viewer.php',`req=${actionbutton.getAttribute('actiongroup')}&type=${type}&id=${id}&key=${actionbutton.parentElement.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent}`
                            ,ajax_answer_string);
                        });
                    }
                }
                // edit buttons create widget
                else if(actionbutton.getAttribute('actiongroup') == 'edit') {
                    actionbutton.addEventListener('click',e=>{
                        e.preventDefault();
                        document.querySelector('#editmodal .modal-body form label').textContent = e.target.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent;
                        if(e.target.parentElement.parentElement.parentElement.id != 'snippetssec') {
                            // if img or file 
                            if(e.target.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent == 'img' ||
                            e.target.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent == 'localurl') {
                                if(document.querySelector('#editmodal .modal-body form textarea')) {
                                    document.querySelector('#editmodal .modal-body form textarea').replaceWith(document.createRange().createContextualFragment(`<input type="file" class="form-control text-bg-dark" name="" value="">`));
                                }
                                document.querySelector('#editmodal .modal-body form input[type="file"]').name = e.target.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent;
                            }
                            // if text
                            else {
                                if(document.querySelector('#editmodal .modal-body form input[type="file"]')) {
                                    document.querySelector('#editmodal .modal-body form input[type="file"]').replaceWith(document.createRange().createContextualFragment(`<textarea class="form-control text-bg-dark" name=""></textarea>`));
                                }
                                document.querySelector('#editmodal .modal-body form textarea').value = e.target.parentElement.parentElement.firstElementChild.textContent;
                                document.querySelector('#editmodal .modal-body form textarea').name = e.target.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent;
                            }
                        }
                        else {
                            document.querySelector('#editmodal .modal-body form textarea').value = e.target.parentElement.parentElement.parentElement.firstElementChild.firstChild.textContent.replace(/^\n */,'').replace(/ *$/,'');
                            document.querySelector('#editmodal .modal-body form textarea').name = e.target.parentElement.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent;
                        }
                        document.querySelector('#editmodal .modal-body form').type.value = type;
                        document.querySelector('#editmodal .modal-body form').id.value = id;
                    });
                }
                // add tags button create widget
                else if(actionbutton.getAttribute('actiongroup') == 'addtag') {
                    actionbutton.addEventListener('click',e=>{
                        e.preventDefault();
                        document.querySelector('#editmodal .modal-body form label').textContent = e.target.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent;
                        if(document.querySelector('#editmodal .modal-body form input[type="file"]')) {
                            document.querySelector('#editmodal .modal-body form input[type="file"]').replaceWith(document.createRange().createContextualFragment(`<textarea class="form-control text-bg-dark" name=""></textarea>`));
                        }
                        document.querySelector('#editmodal .modal-body form textarea').name = e.target.parentElement.parentElement.parentElement.firstElementChild.firstElementChild.textContent;
                        document.querySelector('#editmodal .modal-body form').type.value = type;
                        document.querySelector('#editmodal .modal-body form').id.value = id;
                        myModal.show();
                    });
                }
                // add new snippet create widget
                else if(actionbutton.getAttribute('actiongroup') == 'add') {
                    actionbutton.addEventListener('click',e=>{
                        e.preventDefault();
                        document.querySelector('#editmodal .modal-body form label').textContent = 'new snippet';
                        if(document.querySelector('#editmodal .modal-body form input[type="file"]')) {
                            document.querySelector('#editmodal .modal-body form input[type="file"]').replaceWith(document.createRange().createContextualFragment(`<textarea class="form-control text-bg-dark" name=""></textarea>`));
                        }
                        document.querySelector('#editmodal .modal-body form textarea').name = 'new';
                        document.querySelector('#editmodal .modal-body form textarea').value = '';
                        document.querySelector('#editmodal .modal-body form').type.value = type;
                        document.querySelector('#editmodal .modal-body form').id.value = id;
                        myModal.show();
                    });
                }
            });
        }
    });
    window.addEventListener('ajax_data_received',createrows);
    const myModal = new bootstrap.Modal(document.querySelector('#editmodal'));
    window.addEventListener('ajax_data_received',_=>{
        if(document.querySelector('#editmodal .modal-body  > *.text-danger:last-of-type')) {
            document.querySelector('#editmodal .modal-body  > *.text-danger:last-of-type').remove();
        }
        if(ajax_data_variable.reply) {
            if(ajax_data_variable.reply[0].action == 'fail') {
                document.querySelector('#editmodal .modal-body').appendChild(document
                    .createRange().createContextualFragment(`<div class="text-danger"><i class="fa-solid fa-circle-exclamation">
                    </i><span class="ms-2">${ajax_data_variable.reply[1].cause}</span></div>`));
            }
            if(ajax_data_variable.reply[0].action == 'success') {
                myModal.hide();
            }
            ajax_data_variable.reply[0].action = '';
        }
    });
    document.querySelector('#editmodal').addEventListener('show.bs.modal',_=>{
        if(document.querySelector('#editmodal .modal-body  > *.text-danger:last-of-type')) {
            document.querySelector('#editmodal .modal-body  > *.text-danger:last-of-type').remove();
        }
    });
    document.querySelector('#prevpage').addEventListener('click',e=>{
        e.preventDefault();
        if(ajax_data_variable.previous) {
            id = ajax_data_variable.previous;
            ajax_post('api/viewer.php',`req=view&type=${type}&id=${ajax_data_variable.previous}`
            ,ajax_answer_string);
        }
    });
    document.querySelector('#nextpage').addEventListener('click',e=>{
        e.preventDefault();
        if(ajax_data_variable.next) {
            id = ajax_data_variable.next;
            ajax_post('api/viewer.php',`req=view&type=${type}&id=${ajax_data_variable.next}`
            ,ajax_answer_string);
        }
    });
    document.querySelector('#editmodalbtn').addEventListener('click',e=>{
        e.preventDefault();
        let req = document.querySelector('#editmodal .modal-body form').req.value;
        let type = document.querySelector('#editmodal .modal-body form').type.value;
        let id = document.querySelector('#editmodal .modal-body form').id.value;
        let val = '';
        let key = '';
        if(document.querySelector('#editmodal .modal-body form textarea')) {
            val = document.querySelector('#editmodal .modal-body form textarea').value;
            key = document.querySelector('#editmodal .modal-body form textarea').name;
            ajax_post('api/viewer.php',`req=${req}&type=${type}&id=${id}&key=${key}&val=${val}`
            ,ajax_answer_string);
        }
        else if(document.querySelector('#editmodal .modal-body form input[type="file"]')) {
            val = 'file';
            key = document.querySelector('#editmodal .modal-body form input[type="file"]').name;
            let formdata = new FormData();
            formdata.append('req',req);
            formdata.append('type',type);
            formdata.append('id',id);
            formdata.append('key',key);
            formdata.append('val',val);
            formdata.append('file',document.querySelector('#editmodal .modal-body form input[type="file"]').files[0]);
            ajax_post('api/viewer.php',formdata,ajax_answer_string,false,true);
        }
    });
    document.querySelector('#deletecard').addEventListener('click',_=>{
        ajax_post('api/main.php',`req=delete&type=${type}&id=${id}`,ajax_answer_string);
        setTimeout(_=>{window.location.href = 'wall.php'},100);
    });
});
window.addEventListener('load',vieweronload);