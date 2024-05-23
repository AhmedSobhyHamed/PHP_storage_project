let windowloadlist = (_=>{
    'use strict'
    let listitemsetup = (_=>{
        if(ajax_data_variable.sortlist && 
            Array.isArray(ajax_data_variable.sortlist) &&
            ajax_data_variable.sortlist.length) {
            if(window.gettitle() == 'main' || window.gettitle() == 'manga' 
            || window.gettitle() == 'media' || window.gettitle() == 'notes') {
                window.pageinfo.order ='ascdescname';
            }
            else {
                window.pageinfo.order ='ascdescnamerel';
            }
            document.querySelector('#top-ordering-list').innerHTML = '';
            ajax_data_variable.sortlist.forEach(obj=>{
                let key = Object.entries(obj).map(([key,val])=>key)[0];
                let val = Object.entries(obj).map(([key,val])=>val)[0];
                let listitem = document.createElement('li');
                let listanchor = document.createElement('a');
                listanchor.classList.add('dropdown-item');
                ;
                if(val == 'selected') {
                    listanchor.classList.add('active');
                    // listanchor.classList.add('disabled');
                    document.querySelector('#top-ordering-list').previousElementSibling.textContent = key;
                }
                else {
                    window.pageinfo.order = window.pageinfo.order.replace(val,'');
                    listanchor.setAttribute('href',val);
                    listitem.addEventListener('click',event=>{
                        event.preventDefault();
                        let req = gettitle();
                        ajax_post('api/main.php',`req=${req}&type=new`,ajax_answer_string);
                        ajax_post('api/main.php',`req=${req}&type=li&sort=${val}`,ajax_answer_string);
                        if(window.search_sent)
                        ajax_post('api/main.php',`req=${req}&type=cards&sort=${val}&from=0&search=${document.title}`,ajax_answer_string);
                        else
                        ajax_post('api/main.php',`req=${req}&type=cards&sort=${val}&from=0`,ajax_answer_string);
                    })
                }
                listanchor.textContent = key;
                listitem.appendChild(listanchor);
                document.querySelector('#top-ordering-list').appendChild(listitem);
            })
            ajax_data_variable.sortlist = [];
        }
    });
    window.addEventListener('ajax_data_received',listitemsetup);
});
window.addEventListener('load',windowloadlist);