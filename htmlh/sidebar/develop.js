let windowloadside = (_=>{
    'use strict'
    let newwadgitvalidateevent = (e=>{
        let validing = true;
        if(e.getAttribute('required') != null) {
            if(e.name === 'url') {
                if(!/(^((http|https|ftp|ftps):\/\/)?[\w\d-]+\.[\w\d]{2,3}(\/.*)?$)/.test(e.value)) {
                    e.classList.add('is-invalid');
                    validing &= false;
                }
                else {
                    e.classList.remove('is-invalid');
                }
            }
            else {
                if(!e.value) {
                    validing &= false;
                    e.classList.add('is-invalid');
                }
                else {
                    e.classList.remove('is-invalid');
                }
            }
        }
        if(e.getAttribute('type') == 'file' && e.name == 'img') {
            if(e.files[0] && 
                ((e.files[0].size >2_000_000 || e.files[0].size <= 0) || 
                !(["image/gif","image/jpeg","image/jpg",
                "image/png"].includes(e.files[0].type)))) {
                e.classList.add('is-invalid');
                validing &= false;
            }
            else {
                e.classList.remove('is-invalid');
            }
        }
        if(e.getAttribute('type') == 'file' && e.name == 'file') {
            if(e.files[0] && 
                ((e.files[0].size >500_000_000 || e.files[0].size <= 0) || 
                !(["audio/mpeg","audio/x-wav","audio/wav","audio/mpeg",
                "image/gif","image/jpeg","image/jpg","image/png","video/mpeg",
                "video/mp4","video/x-msvideo"].includes(e.files[0].type)))) {
                e.classList.add('is-invalid');
                validing &= false;
            }
            else {
                e.classList.remove('is-invalid');
            }
        }
        if(e.getAttribute('type') == 'number') {
            if(Number.isNaN(e.value) || e.value < 0) {
                e.classList.add('is-invalid');
                validing &= false;
            }
            else {
                e.classList.remove('is-invalid');
            }
        }
        return validing;
    });
    let sidebaritemsetup = (_=>{
        if(ajax_data_variable.newlist && 
            Array.isArray(ajax_data_variable.newlist) &&
            ajax_data_variable.newlist.length) {
            document.querySelector('#sidebar-list').innerHTML = '';
            ajax_data_variable.newlist.forEach(obj=>{
                let key = Object.entries(obj).map(([key,val])=>key)[0];
                let sideitem = document.createElement('li');
                sideitem.classList.add('list-group-item');
                sideitem.classList.add('rounded');
                sideitem.classList.add('rounded-pill');
                sideitem.classList.add('px-3');
                sideitem.classList.add('py-1');
                sideitem.classList.add('mb-1');
                sideitem.classList.add('bg-success');
                let sideanchor = document.createElement('a');
                sideanchor.classList.add('stretched-link');
                sideanchor.classList.add('link-underline');
                sideanchor.classList.add('link-underline-opacity-0');
                sideanchor.classList.add('link-underline-opacity-0-hover');
                sideanchor.classList.add('text-light');
                sideanchor.setAttribute('data-bs-toggle','modal');
                sideanchor.setAttribute('data-bs-target','#add-new-modal');
                let sideI = document.createElement('i');
                sideI.classList.add('fa-solid');
                sideI.classList.add('fa-file-circle-plus');
                sideI.classList.add('me-1');
                let sidetext = document.createTextNode('add a new ' + key);
                sideanchor.appendChild(sideI);
                sideanchor.appendChild(sidetext);
                sideanchor.addEventListener('click',event=>{
                    event.preventDefault();
                    document.querySelector('#add-new-modal .modal-title').textContent = `add new ${key}`;
                    let body = '';
                    if(key === 'media') {
                        body = `<label for="medianame" class="form-label fw-bold text-black">media title:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="text" name="name" id="medianame" class="form-control" required>
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                required
                            </div>
                        </div>
                        <label for="mediaurl" class="form-label fw-bold text-black">external URL:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="text" name="url" id="mediaurl" class="form-control" required>
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                required
                            </div>
                        </div>
                        <label for="mediafile" class="form-label fw-bold text-black">upload file:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="file" name="file" id="mediafile" class="form-control">
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                optional
                            </div>
                        </div>
                        <label for="mediatagsinsert" class="form-label fw-bold text-black">tags:</label>
                        <div class="row mb-2">
                            <div class="col-10">
                                <div class="bg-dark p-1" id="mediatagscontainer"></div>
                            </div>
                        </div>
                        <div id="add tags" class="row mb-3">
                            <div class="col-10">
                                <input type="text" name="tags" id="mediatagsinsert" class="form-control">
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                <button type="button" id="mediatagsinsertbtn" class="rounded rounded-pill btn btn-success text-capitalize">add tag</button>
                            </div>
                        </div>
                        <label for="mediaimg" class="form-label fw-bold text-black">upload image:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="file" name="img" id="mediaimg" class="form-control">
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                optional
                            </div>
                        </div>
                        <input type="hidden" name="type" value="media">`;
                    }
                    else if(key === 'manga') {
                        body = `<label for="manganame" class="form-label fw-bold text-black">manga title:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="text" name="name" id="manganame" class="form-control" required>
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                required
                            </div>
                        </div>
                        <label for="mangaurl" class="form-label fw-bold text-black">manga website URL:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="text" name="url" id="mangaurl" class="form-control" required>
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                required
                            </div>
                        </div>
                        <label for="mangaimg" class="form-label fw-bold text-black">upload image:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="file" name="img" id="mangaimg" class="form-control">
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                optional
                            </div>
                        </div>
                        <label for="mangadescription" class="form-label fw-bold text-black">description:</label>
                        <div class="row mb-2">
                            <div class="col-10">
                                <input type="text" name="description" id="mangadescription" class="form-control">
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                optional
                            </div>
                        </div>
                        <label for="mangachapter" class="form-label fw-bold text-black">last readed chapter:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="number" name="chapter" id="mangachapter" class="form-control">
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                optional
                            </div>
                        </div>
                        <input type="hidden" name="type" value="manga">`;
                    }
                    else if(key === 'note') {
                        body = `<label for="notename" class="form-label fw-bold text-black">note title:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <input type="text" name="name" id="notename" class="form-control" required>
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                required
                            </div>
                        </div>
                        <label for="snippet" class="form-label fw-bold text-black">first note snippet:</label>
                        <div class="row mb-3">
                            <div class="col-10">
                                <textarea name="snippet" id="snippet" class="form-control"></textarea>
                            </div>
                            <div class="col-2 col-form-label text-dark">
                                optional
                            </div>
                        </div>
                        <input type="hidden" name="type" value="note">`;
                    }
                    document.querySelector('#newform').innerHTML = '';
                    document.querySelector('#newform').append(document.createRange().createContextualFragment(body));
                    if(document.getElementById('mediatagsinsert')) {
                        document.querySelector('#mediatagsinsertbtn').addEventListener('click', event=>{
                            event.preventDefault();
                            let data = document.querySelector('#mediatagsinsert').value;
                            if(data) {
                                let ele = document.createElement('input');
                                ele.type = 'hidden';
                                ele.name = 'tags[]';
                                ele.value = data;
                                document.querySelector('#newform').appendChild(ele);
                                let tag = document.createElement('div');
                                tag.classList.add('badge');
                                tag.classList.add('m-1');
                                tag.classList.add('rounded-pill');
                                tag.classList.add('text-bg-success');
                                tag.textContent = data;
                                document.querySelector('#mediatagscontainer').appendChild(tag);
                                document.querySelector('#mediatagsinsert').value = '';
                            }
                        });
                    }
                    Array.from(document.querySelectorAll
                        ('#newform input , #newform textarea , #newform #mediatagscontainer'))
                        .forEach(e=>{
                            e.addEventListener('blur',_=>{
                                if(window.newreadytovalid) {
                                    newwadgitvalidateevent(e);
                                }
                            });
                        });
                    let newreadytovalid = false;
                    window.newreadytovalid = newreadytovalid;
                })
                sideitem.appendChild(sideanchor);
                document.querySelector('#sidebar-list').appendChild(sideitem);
                document.querySelector('#add-new-modal #newform').addEventListener('submit',event=>{
                    event.preventDefault();
                });
                document.querySelector('#add-new-modal #mediasubmit').onclick = (event=>{
                    event.preventDefault();
                    event.stopPropagation();
                    let data = '';
                    let validing = true;
                    let formdata = new FormData();
                    Array.from(document.querySelectorAll
                        ('#newform input , #newform textarea , #newform #mediatagscontainer'))
                        .forEach(e=>{
                        if(e.id !== 'mediatagsinsert') {
                            if(e.type == 'file') {
                                if(e.files[0]) {
                                    formdata.append(e.name,e.files[0]);
                                    formdata.append(e.name,e.name);
                                }
                            }
                            else {
                                if(e.value) {
                                    formdata.append(e.name,e.value);
                                }
                            }
                            // data += `${e.name}=${e.value}&`;
                            validing &= newwadgitvalidateevent(e);
                            window.newreadytovalid = true;
                        }
                    });
                    formdata.append('req','new');
                    // data = data.substring(0,data.length-1);
                    if(validing == true) {
                        const modal = new bootstrap.Modal('#create-new-status', {
                            keyboard: false
                        })
                        modal.show();
                        ajax_post('api/createnew.php',formdata,ajax_answer_string,false,true);
                    }
                    // document.querySelector('#newform').classList.add('was-validated');
                });
            });
            ajax_data_variable.newlist = [];
        }
    });
    window.addEventListener('ajax_data_received',sidebaritemsetup);
    let clicktocancel = (_=>{
        window.dispatchEvent(AjaxcancelingEvent);
    });
    window.addEventListener('ajax_uploading',_=>{
        if(window.ajax_upload_ratio) {
            let body = document.createElement('p');
            body.textContent = window.ajax_upload_ratio+'%';
            if(!document.querySelector('#upload-event-bar')) {
                let bar = document.createElement('div');
                bar.classList.add('progress');
                bar.id = 'upload-event-bar';
                let inpar = document.createElement('div');
                inpar.classList.add('progress-bar');
                inpar.classList.add('progress-bar-striped');
                inpar.classList.add('progress-bar-animated');
                inpar.classList.add('bg-success');
                bar.appendChild(inpar);
                document.querySelector('#create-new-status .modal-body').appendChild(body);
                document.querySelector('#create-new-status .modal-body').appendChild(bar);
                inpar.style.width = window.ajax_upload_ratio+'%';
            }
            else {
                document.querySelector('#create-new-status .modal-body .progress-bar').
                style.width = window.ajax_upload_ratio+'%';
            }
            document.querySelector('#create-new-status button').textContent = 'cancel';
            document.querySelector('#create-new-status button').addEventListener('click',clicktocancel);
            // if(window.ajax_upload_ratio != '100.00') {
            //     document.querySelector('#create-new-status button').classList.add('disabled');
            //     document.querySelector('#create-new-status button').textContent = 'cancel';
            // }
            // else {
            //     document.querySelector('#create-new-status button').classList.remove('disabled');
            //     document.querySelector('#create-new-status button').textContent = 'close';
            // }
        }
    });
    window.addEventListener('ajax_data_received',_=>{
        if(ajax_data_variable.reply) {
            if(ajax_data_variable.reply[0].action == 'fail') {
                if(document.querySelector('#create-new-status .modal-body > *.text-danger:last-of-type'))
                    document.querySelector('#create-new-status .modal-body > *.text-danger:last-of-type').remove();
                if(document.querySelector('#create-new-status .modal-body > *.text-success:last-of-type'))
                    document.querySelector('#create-new-status .modal-body > *.text-success:last-of-type').remove();
                document.querySelector('#create-new-status .modal-body').appendChild(document
                    .createRange().createContextualFragment(`<div class="text-danger"><i class="fa-solid fa-circle-exclamation">
                    </i><span>${ajax_data_variable.reply[1].cause}</span></div>`));
                    ajax_data_variable.reply[0].action = '';
            }
            else if(ajax_data_variable.reply[0].action == 'success') {
                if(document.querySelector('#create-new-status .modal-body > *.text-danger:last-of-type'))
                    document.querySelector('#create-new-status .modal-body > *.text-danger:last-of-type').remove();
                if(document.querySelector('#create-new-status .modal-body > *.text-success:last-of-type'))
                    document.querySelector('#create-new-status .modal-body > *.text-success:last-of-type').remove();
                document.querySelector('#create-new-status .modal-body').appendChild(document
                    .createRange().createContextualFragment(`<div class="text-success"><i class="fa-solid fa-circle-check"></i>
                    <span>${ajax_data_variable.reply[0].action}</span></div>`));
                    document.querySelector('#create-new-status button').textContent = 'close';
                    document.querySelector('#create-new-status button').removeEventListener('click',clicktocancel);
                    ajax_data_variable.reply[0].action = '';
                    let req = gettitle();
                    ajax_post('api/main.php','req='+req,ajax_answer_string);
            }
        }
    })
});
window.addEventListener('load',windowloadside);