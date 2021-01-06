/**
 * This removes a card from the DOM.
 *
 * @param {Object} obj - The card to remove
 *
 */
    function hideCard(obj)
    {
        obj.parent().parent().addClass("d-none");
    };
/**
 * This removes a card from the DOM.
 *
 * @param {Object} obj - The card to remove
 * @param {String} id - The data-id attribute of the attatchment
 */
    function hideCardRmFile(obj, id)
    {
        obj.parent().parent().addClass("d-none");
        framework.ajax(base+'/ajax/bean/upload/'+id+'/', {
            method: 'DELETE',
            fail : function(jx){ bootbox.alert('<h3>Delete failed</h3>'+jx.responseText); },
        });
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
        let attatchment = $(this).parent().siblings(".attatchment").attr("data-id");
        let nid = $(this).parent().parent().attr("data-id");
        let card = $(this).parent().parent();
        if (attatchment)
        {
            framework.deletebean(e, this, e.data.type, nid, function(){hideCardRmFile(card, attatchment);}, "");
        }
        else 
        {
            framework.deletebean(e, this, e.data.type, nid, function(){hideCard(card);}, "");
        }  
    };
/**
 * Toggles the contributor form on the project page
 *
 * @param e - The event reference
 *
 */
    function toggleContributors(e) 
    {
        $(this).toggleClass('fa-toggle-on');
        $('#contribForm').toggleClass('d-none');
    };

/**
 * Updates a bean from a modal form
 * Expects to be called on the button that opens the form
 * @param e - The event reference
 *
 */
    function editItem(e) 
    {
        let mID = $(this).parent().attr('data-target');
        $('#'+mID).modal('show');
        $('#'+e.data.formName+mID).on('submit', function(event){
            event.preventDefault();
            $.ajax({
                type: 'PATCH',
                url: base+'/ajax/update/'+e.data.type+'/'+$(this).parent().attr('data-id'),
                data: $(this).serialize(),
                error: function(jx) { bootbox.alert('<h3>Update failed</h3>'+jx.responseText); }
            });
            $('#'+mID).modal('hide');
        });
    };