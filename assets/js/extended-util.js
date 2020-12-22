/**
 * This removes a card from the DOM.
 *
 * @param {Object} obj - The card to remove
 *
 */
    function todel(obj){
        obj.parent().parent().addClass("d-none");
    };
/**
 * This function makes an ajax call to delete the bean pointed to by type with 
 * the data-id attribute.
 *
 * @param e - The event reference
 *
 */
    function delbeanDomRemove(e)
    {
        let nid = $(this).parent().parent().attr("data-id");
        let card = $(this).parent().parent();
        framework.deletebean(e, this, e.data.type, nid, function(){todel(card);}, "");
    };
/**
 * Toggles the contributor form on the project page
 *
 * @param e - The event reference
 *
 */
    function toggleContributors(e) {
        $(this).toggleClass('fa-toggle-on');
        $('#contribForm').toggleClass('d-none');
    };