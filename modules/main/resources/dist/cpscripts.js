/*

DOM Structure:

  #sidebar
    nav
      ul
       li.heading <-- toggle class 'collapsed' here
         span <-- The heading text, add click eventListener here
         ul   <-- the sources, toggle visiblity via css .collapsed -> ul

CSS:

    #sidebar li.heading > span {
        cursor: pointer;
    }

    #sidebar li.heading > span::after {
        content: '  [-]';
        font-weight: normal;
        font-size: 12px;
    }
    #sidebar li.heading.collapsed > span::after {
        content: '  [+]';
    }


    #sidebar li.heading.collapsed ul {
        display: none;
    }

*/


['entries','assets','categories','users'].forEach(elementType => {
    if (isElementIndex(elementType)) {
        initSidebarVisibility(elementType)
    }
})


function isElementIndex(elementType) {
    urlSegments = window.location.pathname.split("/");
    return urlSegments.length >= 3 && urlSegments[2] == elementType
}

function initSidebarVisibility(elementType) {
    // get headings
    headingNodes = document.querySelectorAll('#sidebar li.heading > span')


    // set visibility as stored in localStorage
    setSidebarVisibility(elementType, headingNodes);

    // Toggle sources visiblity on click
    headingNodes.forEach(item => {
        item.addEventListener('click', event => {
            event.target.parentElement.classList.toggle('collapsed')
            storeSidebarVisibility(elementType, headingNodes)
        })
    })
}

// store settings in local storage
function storeSidebarVisibility(elementType, headingNodes) {
    v = {};
    headingNodes.forEach(function(element) {
        v[element.innerText] = element.parentElement.classList.contains('collapsed') ? 'hidden' : 'visible'
        // v.push(element.parentElement.classList.contains('collapsed') ? 'hidden' : 'visible')
    });
    localStorage['sidebarVisiblity_' + elementType] = JSON.stringify(v);
}

function setSidebarVisibility(elementType, headingNodes) {
    var v = localStorage['sidebarVisiblity_' + elementType];

    // No stored settings?
    if (v === undefined) {
        return;
    }

    v = JSON.parse(v);

    console.log(v)


    headingNodes.forEach( (element, index) => {
        if (v[element.innerText] == 'hidden') {
            element.parentElement.classList.add('collapsed');
        }
    })

}
