jQuery(document).ready(function($){
  // Hide the toggle containers on load.
  $('.toggle_container').hide();

  // Switch the active state per click.
  $('h3.trigger').toggle(
      function(){ $(this).addClass('active'); }, 
      function(){ $(this).removeClass('active'); }
  );

  // Actions to perform on click.
  $('h3.trigger').click(function(){ 
    // Open or close the associated container.
    var toggle_container = $(this).next('.toggle_container');
    toggle_container.slideToggle();
    
    // Check if we have opened the container and previously loaded this book.
    if( $(this).hasClass('active') && !($(this).hasClass('loaded')) ){
      var metadata = toggle_container.find('.metadata');
      var google_book_id = metadata.attr('title');
      
      var gbs_data_failure_callback = function(response, status, error){ alert("I'm sorry, we encountered a problem trying to look up this volume.\nError: " + error + "\nStatus: " + status + "\n" + response.responseText); };
      var gbs_data_success_callback = function(response, status){
        var entry = $(response).find('entry');
        var google_book_info_uri = entry.find('link[rel$=info]').attr('href');
        metadata.append(
          $('<a>').attr('href', google_book_info_uri).append(
              $('<img>').addClass('book-thumbnail')
                        .attr('src', entry.find('link[rel$=thumbnail]').attr('href'))
          )
        );
        
        // The collection of authors.
        var author_string = entry.find('dc\\:creator').map(function(){ return $(this).text(); }).get().join(', ');
        
        // Determine the viewability status of the work.
        var viewability = entry.find('gbs\\:viewability').attr('value');
        var viewability_value = viewability.substr(viewability.indexOf('#') + 1, viewability.length - 1);
        var viewability_status = '';
        
        switch( viewability_value ){
          case 'view_all_pages': 
              viewability_status = 'open; possibly public domain.';
              break;
          case 'view_partial': 
              viewability_status = 'copyrighted; limited page views.';
              break;
          case 'view_no_pages': 
              viewability_status = 'copyrighted; snippet views only.';
              break;
          default: 
              viewability_status = 'status unknown.';
        }
        
        metadata.append(
          $('<ul>').addClass('book-metadata')
              .append(
                $('<li>').addClass('book-title').append(
                  $('<a>').attr('href', google_book_info_uri).text(entry.find('title').text())
                )
              )
              .append(
                $('<li>').addClass('book-author').text(author_string)
              )
              .append(
                $('<li>').addClass('book-publisher').text(entry.find('dc\\:publisher').text() + ' (' + entry.find('dc\\:date').text() + ')')
              )
              .append(
                $('<li>').addClass('book-viewable').text('Viewability: ' + viewability_status)
              )
        );

        var identifiers = '';
        entry.find('dc\\:identifier').each(function(index){
          identifiers += "<li class='metadata-identifier'>" + $(this).text() + "</li>\n";
        });
        var embeddable = "<li class='metadata-embeddable'>Embeddable: " + entry.find('gbs\\:embeddability').attr('value') + "</li>\n";
        var open_access = "<li class='metadata-open_access'>Open Access: " + entry.find('gbs\\:openAccess').attr('value') + "</li>\n";
      };

      // Perform the Google Book Search Data API AJAX request through our server proxy.
      $.ajax({
        url:      '/wp-content/plugins/shortcode-manager/handlers/google_books/proxy.php',
        data:     { target_uri: 'http://www.google.com/books/feeds/volumes/' + google_book_id },
        type:     'GET',
        dataType: 'xml',
        success:  gbs_data_success_callback,
        error:    gbs_data_failure_callback
      });

      // TODO: check if embeddable.
      var canvas = toggle_container.find('.canvas');
      var page = canvas.attr('title');
      var highlight = canvas.text();
      var viewer = new google.books.DefaultViewer(canvas[0]);
      var failure_callback = function(){ alert("I'm sorry, we encountered a problem trying to load this volume."); };
      var success_callback = function(){ viewer.highlight(highlight); viewer.goToPage(page); };
      viewer.load(google_book_id, failure_callback, success_callback);

      // Add page number functions.
      toggle_container.find('a.previous_page').click(function(){
        viewer.previousPage();
        toggle_container.find('.current_page').text(viewer.getPageNumber());
      });
      toggle_container.find('a.jump_to_start_page').click(function(){
        viewer.goToPage(page);
        toggle_container.find('.current_page').text(page);
      });
      toggle_container.find('a.get_current_page').click(function(){
        $(this).next('.current_page').text(viewer.getPageNumber());
      });
      toggle_container.find('a.next_page').click(function(){
        viewer.nextPage();
        toggle_container.find('.current_page').text(viewer.getPageNumber());
      });
      
      // Add a flag indicating we've loaded this Google Book and clean up the title
      // attributes used for passing data.
      $(this).addClass('loaded');
      metadata.attr('title', '');
      canvas.attr('title', '');
    }
  });

});
