let windowloadmaincontent = (_=>{
    'use strict'
    let deletebuttonevent = function (e) {
        let ele = e.target;
        if(e.target.nodeName === 'I') {
            ele = e.target.parentElement;
        }
        let reqType = ele.previousSibling.previousSibling.lastElementChild.previousSibling.previousSibling.value;
        let reqID   = ele.previousSibling.previousSibling.lastElementChild.previousSibling.previousSibling.previousSibling.previousSibling.value;
        ajax_post('api/main.php',`req=delete&type=${reqType}&id=${reqID}`,ajax_answer_string);
        ele.parentElement.parentElement.parentElement.remove();
    };
    let createcard = ((name,describtion,date,type,id,sendpage,image='',chapter=0)=>{
        if(type == 'manga' || type == 'media') {
            let card = `<div class="col">
                <div class="card h-100 text-bg-dark border-primary">
                    <div class="card-header position-relative p-0">
                        <img src="${image}" class="card-img-top d-block img-fluid bg-primary min-height-img" alt="card img">
                        <div class="card-img-overlay">
                            <div class="position-absolute bottom-0 bg-dark bg-opacity-50 start-0 end-0 p-1">
                                <h5 class="card-title text-uppercase">${name}</h5>
                                <h5 class="card-subtitle  mb-2 text-body-secondary text-capitalize">${type}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <blockquote class="blockquote fs-6 mb-0">
                            <p class="card-text overflow-hidden fixed-height-6">${describtion}</p>`;
            if(gettitle() === 'manga') {
                card += `<footer class="blockquote-footer d-flex justify-content-between">${date}
                            <span class="badge rounded-pill text-bg-light">${chapter}</span>
                        </footer>`;
            }
            else {
                card += `<footer class="blockquote-footer">${date}
                        </footer>`;
            }
            card+=`</blockquote>
                    </div>
                    <div class="card-footer d-flex column-gap-2">
                        <form action="${sendpage}" method="post" class="flex-grow-1">
                            <input type="hidden" name="req" value="view">
                            <input type="hidden" name="id" value="${id}">
                            <input type="hidden" name="type" value="${type}">
                            <button type="submit" class="w-100 text-capitalize btn btn-outline-primary stretched-link">open</button>
                        </form>
                        <button type="button" id="deletecard" class="z-2 text-capitalize btn btn-outline-danger">del
                        <i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
            </div>`;
            // document.querySelector('#maincontent').append(card);
            document.querySelector('#maincontent').append(document.createRange().createContextualFragment(card));
        }
        else if(type == 'notes') {
            let card = `<div class="col">
                <div class="card h-100 text-bg-dark border-primary">
                    <div class="card-header bg-success">
                        <h5 class="card-title text-uppercase">${name}</h5>
                        <h5 class="card-subtitle  mb-2 text-body-secondary text-capitalize">${type}</h5>
                    </div>
                    <div class="card-body">
                        <blockquote class="blockquote fs-6 mb-0">
                            <p class="card-text overflow-hidden fixed-height-15">${describtion}</p>
                            <footer class="blockquote-footer">${date}</footer>
                        </blockquote>
                    </div>
                    <div class="card-footer d-flex column-gap-2">
                        <form action="${sendpage}" method="post" class="flex-grow-1">
                            <input type="hidden" name="req" value="view">
                            <input type="hidden" name="id" value="${id}">
                            <input type="hidden" name="type" value="${type}">
                            <button type="submit" class="w-100 text-capitalize btn btn-outline-primary stretched-link">open</button>
                        </form>
                        <button type="button" id="deletecard" class="z-2 text-capitalize btn btn-outline-danger">del
                        <i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
            </div>`;
            // document.querySelector('#maincontent').append(card);
            document.querySelector('#maincontent').append(document.createRange().createContextualFragment(card));
        }
        
    });
    let cardAreasetup = (_=>{
        if(ajax_data_variable.cards && 
            Array.isArray(ajax_data_variable.cards) &&
            ajax_data_variable.cards.length) {
            if(!ajax_data_variable.cards[0][1]) {
                document.querySelector('#maincontent').innerHTML = '';
                window.pageinfo.cardnum = 0;
            }
            ajax_data_variable.cards.forEach(obj=>{
                if(obj[0].type !== 'cards')
                {
                    // let keys = [];
                    // let vals = [];
                    // obj.forEach(object=>{
                    //     keys.push(Object.entries(object).map(([key,val])=>key)[0]);
                    //     vals.push(Object.entries(object).map(([key,val])=>val)[0]);
                    // })
                    if(gettitle() ===   'main') {
                        createcard(obj[0].name,obj[1].description.replace('}{',' ')
                        .replace(/^{/,'').replace(/}$/,''),obj[3].date,
                            obj[5].type,obj[4].id,'viewer.php',obj[2].img);
                    }
                    if(gettitle() ===  'media') {
                        createcard(obj[0].name,obj[1].tags.replace('}{',' ').replace(/^{/,'')
                        .replace(/}$/,''),obj[3].date,obj[5].type,obj[4].id,
                        'viewer.php',obj[2].img);
                    }
                    if(gettitle() ===  'manga') {
                        createcard(obj[0].name,obj[2].description,obj[5].date,
                            obj[6].type,obj[4].id,'viewer.php',obj[1].img,obj[3].chapter);
                    }
                    if(gettitle() ===  'notes') {
                        createcard(obj[0].name,'',obj[2].date,obj[3].type,obj[1].id,
                            'viewer.php');
                    }
                    if(gettitle() === 'search') {
                        createcard(obj[0].name,obj[1].description.replace('}{',' ')
                        .replace(/^{/,'').replace(/}$/,''),obj[3].date,
                            obj[5].type,obj[4].id,'viewer.php',obj[2].img);
                    }
                    window.pageinfo.cardnum++;
                }
            });
            Array.from(document.querySelectorAll('#deletecard')).forEach(e=>{
                e.addEventListener('click',deletebuttonevent);
            });
        }
        ajax_data_variable.cards = [];
    });
    window.addEventListener('ajax_data_received',cardAreasetup);
    // add event to scroll
    let delay = false;
    document.addEventListener('scroll',e=>{
        let bodyheight = Math.max(document.body.scrollHeight,document.body.offsetHeight
            ,document.documentElement.scrollHeight,document.documentElement.offsetHeight
            ,document.documentElement.clientHeight);
        if(window.scrollY > bodyheight-100-document.documentElement.clientHeight) {
            if(!delay) {
                ajax_post('api/main.php',`req=${gettitle()}&type=cards&sort=${window.pageinfo.order}&from=${window.pageinfo.cardnum}`,ajax_answer_string);
                delay = true;
                setTimeout(_=>{delay = false;},2000);
            }
        }
    });
});
window.addEventListener('load',windowloadmaincontent);