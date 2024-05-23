window.onload = (()=> {
    // valiate routen 
    'use strict'
    let is_an_email = (email=>{
        return /^[\w\d\._\-]+@[\w\d]+\.[\w\d]{2,3}$/.test(email);
    })
    let is_a_password = (paswd=>{
        return /^[^\'\"><(){}]{10,}$/.test(paswd);
    })
    // submit event
    document.querySelector('.need-validate').addEventListener('submit', event=>{
        let validing = false;
        validing = is_an_email  (event.target.em.value);
        validing = is_a_password(event.target.pw.value);
        if(validing === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        event.target.classList.add('was-validated');
    })

    // eye key
    let passwordeye = (event,action)=>{
        event.preventDefault();
        event.stopPropagation();
        if(action) {
            if(event.target.nodeName.toLowerCase() === 'i') {
                event.target.parentElement.previousElementSibling.setAttribute('type','text');
            }
            else {
                event.target.previousElementSibling.setAttribute('type','text');
            }
        }
        else {
            if(event.target.nodeName.toLowerCase() === 'i') {
            event.target.parentElement.previousElementSibling.setAttribute('type','password');
            }
            else {
                event.target.previousElementSibling.setAttribute('type','password');
            }
        }
    }
    document.querySelector('button#pw-viewer').addEventListener('click',event=>{
        passwordeye(event,true)
    });
    document.querySelector('button#pw-viewer').addEventListener('mousedown',event=>{
        passwordeye(event,true)
    });
    document.querySelector('button#pw-viewer').addEventListener('mouseenter',event=>{
        passwordeye(event,true)
    });
    document.querySelector('button#pw-viewer').addEventListener('mouseup',event=>{
        passwordeye(event,false)
    });
    document.querySelector('button#pw-viewer').addEventListener('mouseleave',event=>{
        passwordeye(event,false)
    });
})
