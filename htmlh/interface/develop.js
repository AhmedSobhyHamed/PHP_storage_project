let oninterfaceload = (_=>{
    Array.from(document.querySelectorAll('.nav-item a')).forEach(ele=>{
        ele.addEventListener('click',_=>{
            Array.from(document.querySelectorAll('.nav-item a')).forEach(ele=>{
                ele.classList.remove('active');
            });
            ele.classList.add('active');
        });
    });
});
window.addEventListener('load',oninterfaceload);