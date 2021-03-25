import Choices from "choices.js";

if (document.readyState === "complete" || document.readyState === "interactive") {
    domReady()
} else {
    window.addEventListener("DOMContentLoaded", domReady)
}
function domReady() {

    var alertCloseBtn = Array.from(document.querySelectorAll('.alert button'))
    alertCloseBtn.forEach(el => {
        el.onclick = function () {
            el.parentElement.style.opacity = 0;
            setTimeout(function () {
                el.parentNode.remove()
            }, 550)
        }
    })
    if (document.readyState === 'complete') {
        allReady()
    }else{
        document.onreadystatechange = function () {
            if (document.readyState === 'complete') {
                allReady()
            }
        }
    }
}
function allReady() {
    setTimeout(function () {
        document.querySelector('body').classList.remove('no-js');
    }, 100)

    let selects = document.querySelectorAll('select');
    if (selects){
        selects.forEach(select => {
            new Choices(select,{
                removeItemButton: true,
            });
        })
    }

    let trigger = document.querySelector('.sidebar-toggle');
    if (trigger){
        let sidebar = document.querySelector('.sidebar');
        let dashboardContent = document.querySelector('.dashboard .content:first-of-type');
        let sidebarState = false;
        trigger.onclick = function(){
            trigger.classList.toggle('open')
            sidebar.classList.toggle('active')
            if (sidebarState){
                sidebarState = false
            }else {
                sidebarState = true;
                dashboardContent.addEventListener('click', clickOutsideSidebar)
                function clickOutsideSidebar() {
                    trigger.classList.remove('open')
                    sidebar.classList.remove('active')
                    dashboardContent.removeEventListener('click', clickOutsideSidebar)
                }
            }
        }
    }
}