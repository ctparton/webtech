/**
 * This removes a card from the DOM using Bootstrap display properties.
 *
 * @param {object} obj - The card node to remove
 *
 * @return {void}
 */
    function hideCard(obj)
    {
        obj.parent().parent().addClass("d-none");
    };

/**
 * This removes a card from the DOM using Bootstrap display properties and makes an ajax call to 
 * DELETE an attachment associated with it.
 *
 * @param {object} obj - The card node to remove
 * @param {int} id - The data-id attribute of the attachment
 * 
 * @return {void}
 */
    function hideCardRmFile(obj, id)
    {
        obj.parent().parent().addClass("d-none");
        framework.ajax(base + '/ajax/bean/upload/' + id + '/', {
            method: 'DELETE',
            fail : function(jx){ bootbox.alert('<h3>Delete failed</h3>'+jx.responseText); },
        });
    };

/**
 * This function makes an ajax call to delete the bean pointed to by type with 
 * the data-id attribute, also removes attachment if the note has one
 *
 * @param {object} e - The event reference
 *
 * @return {void}
 */
    function delbeanDomRemove(e)
    {
        const attatchment = $(this).parent().siblings(".attatchment").attr("data-id");
        const nid = $(this).parent().parent().attr("data-id");
        const card = $(this).parent().parent();
        if (attatchment)
        {
            framework.deletebean(e, this, e.data.type, nid, function() { hideCardRmFile(card, attatchment); }, "");
        }
        else 
        {
            framework.deletebean(e, this, e.data.type, nid, function() { hideCard(card); }, "");
        }  
    };

/**
 * Toggles the contributor form on the project page
 *
 * @param {object} e - The event reference
 *
 * @return {void}
 */
    function toggleContributors(e) 
    {
        $(this).toggleClass('fa-toggle-on');
        $('#contribForm').toggleClass('d-none');
    };

/**
 * Updates a bean from a modal form
 * Expects to be called on the button that opens the form
 * @param {object} e - The jQuery event 
 *
 * @return {void}
 */
    function editItem(e) 
    {
        const mID = $(this).parent().attr('data-target');
        $('#' + mID).modal('show');
        $('#' + e.data.formName + mID).on('submit', function(event)
        {
            event.preventDefault();
            $.ajax({
                type: 'PATCH',
                url: base + '/ajax/update/' + e.data.type + '/' + $(this).parent().attr('data-id'),
                data: $(this).serialize(),
                success: function(data, txt) { if (txt === 'success') { location.reload(); } }, 
                error: function(jx) { bootbox.alert('<h3>Update failed</h3>'+jx.responseText); }
            });
            $('#' + mID).modal('hide');
        });
    };

/**
* Converts any plain text links in <p> note text to a <a> link tags
* @param {string} text - innerHTML of <p> note text
*
* @return {string} - input text regex replaced with <a> if necessary
*/
    function convertToLinks(text)
    {
        const urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
        return text.replace(urlRegex, function(url)
        {
            return '<a href="' + url + '">' + url + '</a>';
        })
    }