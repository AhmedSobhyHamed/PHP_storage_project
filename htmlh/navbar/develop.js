let windowloadnav = (_=>{
    'use strict';
    // correcting screen procedure
    if(window.innerWidth <= 992) {
        document.querySelector('.navbar #toggle-section-2').classList.remove('d-none');
        document.querySelector('.navbar #toggle-section-1').classList.remove('d-none');
        document.querySelector('.navbar .dropdown').classList.remove('d-none');
    }
    // nav bar scrolling event callback function
    let navscrolling = (_=>{
        if(window.scrollY >250) {
            document.querySelector('.navbar').classList.add('sticky-top');
            document.querySelector('.navbar #toggle-section-1').classList.add('d-none');
            document.querySelector('.navbar #toggle-section-2').classList.remove('d-none');
            if(window.innerWidth <= 992) {
                document.querySelector('.navbar #toggle-section-2').classList.add('d-none');
                document.querySelector('.navbar #toggle-section-1').classList.add('d-none');
                document.querySelector('.navbar .dropdown').classList.add('d-none');
            }
        }
        if(window.scrollY <=250) {
            document.querySelector('.navbar').classList.remove('sticky-top');
            document.querySelector('.navbar #toggle-section-2').classList.add('d-none');
            document.querySelector('.navbar #toggle-section-1').classList.remove('d-none');
            if(window.innerWidth <= 992) {
                document.querySelector('.navbar #toggle-section-2').classList.remove('d-none');
                document.querySelector('.navbar #toggle-section-1').classList.remove('d-none');
                document.querySelector('.navbar .dropdown').classList.remove('d-none');
            }
        }
    });
    // nav bar resize page event callback function
    let navresize = (_=>{
        navscrolling();
        if(window.innerWidth > 992) {
            document.querySelector('.navbar .dropdown').classList.remove('d-none');
        }
    });
    // nav logo go to top event callback function
    let navbartotop =  (event=>{
        event.preventDefault();
        window.scrollTo(0,0);
    });
    // add event listener
    window.addEventListener('scroll',navscrolling);
    window.addEventListener('resize',navresize);
    document.querySelector('a.navbar-brand').addEventListener('click',navbartotop);
    document.querySelector('.navbar form').addEventListener('submit',event=>{
        // ajax request for search and maintaine result
        event.preventDefault();
        window.search_sent = true;
        document.title = event.target.search.value;
        window.pageinfo.type = window.gettitle();
        ajax_post('api/main.php',`req=search&type=new`,ajax_answer_string);
        ajax_post('api/main.php',`req=search&type=li&sort=rel`,ajax_answer_string);
        ajax_post('api/main.php',`req=search&type=cards&sort=rel&from=0&search=${event.target.search.value}`,ajax_answer_string);
    });
    document.querySelectorAll('a[href="#site-color"]').forEach(ele=> {
        ele.addEventListener('click',event=>{
        // open a window to sellect the color and store it for later and change theme of site
        event.preventDefault();
        event.stopPropagation();
        
        })
    });
    document.querySelectorAll('#navbarContent ul li a').forEach(ele=>{
        ele.addEventListener('click',e=>{
            e.preventDefault();
            document.title = e.target.textContent;
            if(e.target.textContent == 'home')  document.title = 'main wall';
            window.pageinfo.type = window.gettitle();
            Array.from(e.target.parentElement.parentElement.children).forEach(e=>{
                e.firstElementChild.classList.remove('active');
            })
            e.target.classList.add('active');
            let req = gettitle();
            window.search_sent = false;
            ajax_post('api/main.php','req='+req,ajax_answer_string);
        });
    });
});
window.addEventListener('load',windowloadnav);










