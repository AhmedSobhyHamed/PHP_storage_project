window.onload = ()=> {
    // valiate routen 
    'use strict'
    let is_a_name = (name=>{
        return /^[A-Za-z]+$/.test(name);
    });
    let is_an_email = (email=>{
        return /^[\w\d\._\-]+@[\w\d]+\.[\w\d]{2,3}$/.test(email);
    })
    let is_a_password = (paswd=>{
        return /^[^\'\"><(){}]{10,}$/.test(paswd);
    })
    // change event 
    // presskey event
    // blur event
    // good valid with time
    let validingevent = (event=>{
        event.stopPropagation()
        let result = false;
        switch(event.target.id) {
            case 'fn':
            case 'ln':
                result = is_a_name(event.target.value);
                break;
            case 'em':
                result = is_an_email(event.target.value);
                break;
            case 'pw':
                result = is_a_password(event.target.value);
                break;
        }
        if(!document.querySelector('.need-validate').classList.contains('was-validated') 
        && result === true) {
            event.target.classList.add('is-valid');
            event.target.classList.remove('is-invalid');
            setTimeout(_=>{
                if(event.target.id !== 'pw')
                event.target.classList.remove('is-valid');
            }, 5000);
        }
        else if(!document.querySelector('.need-validate').classList.contains('was-validated') 
        && result === false) {
            event.target.classList.add('is-invalid');
            event.target.classList.remove('is-valid');
        }
    })
    document.querySelectorAll('input').forEach(input=>{
        input.addEventListener('change',validingevent);
        input.addEventListener('keypress',validingevent);
        input.addEventListener('blur',validingevent);
    })
    // submit event
    document.querySelector('.need-validate').addEventListener('submit', event =>{
        let validing = true;
        validing &= is_a_name    (event.target.fn.value);
        validing &= is_a_name    (event.target.ln.value);
        validing &= is_an_email  (event.target.em.value);
        validing &= is_a_password(event.target.pw.value);
        if(validing == false) {
            event.preventDefault();
            event.stopPropagation();
            let blur = new Event('blur');
            event.target.fn.dispatchEvent(blur);
            event.target.ln.dispatchEvent(blur);
            event.target.em.dispatchEvent(blur);
            event.target.pw.dispatchEvent(blur);
        }
        // event.target.classList.add('was-validated');
        // event.target.fn.classList.remove('is-invalid');
        // event.target.ln.classList.remove('is-invalid');
        // event.target.em.classList.remove('is-invalid');
        // event.target.pw.classList.remove('is-invalid');
        // event.target.fn.classList.remove('is-valid');
        // event.target.ln.classList.remove('is-valid');
        // event.target.em.classList.remove('is-valid');
        // event.target.pw.classList.remove('is-valid');
    })

    // eye key
    let passwordeye = (event,action)=>{
        event.preventDefault();
        event.stopPropagation();
        if(action)  event.target.previousElementSibling.setAttribute('type','text');
        else        event.target.previousElementSibling.setAttribute('type','password');
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
}
