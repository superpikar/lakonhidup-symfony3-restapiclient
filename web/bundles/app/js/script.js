function initPage(rootURL, taxonomy, term){
  // remove next-page-identifier element
  var $postList = $('.post-list');
  var $loadMore = $('.load-more');

  if(!_.isUndefined(taxonomy)&&!_.isUndefined(term)){
    apiURI = rootURL+'morePosts/'+taxonomy+'/'+term+'/';
  }
  else{
    apiURI = rootURL+'morePosts/';
  }

  $loadMore.on('click', function(){
    $loadMore.addClass('is-loading');
    var pageNumber = $postList.data('page') + 1;
    $.ajax({
      url: apiURI + pageNumber
    }).done(function(html) {
      // if doesnt have content, then hide load-more button
      if(html.length < 5 ){
        $loadMore.removeClass('is-loading');
        $loadMore.addClass('is-disabled');
        $loadMore.text($loadMore.data('fulltext'));
      }
      else{
        $loadMore.removeClass('is-loading');
        $postList.data('page', pageNumber);
        $postList.append(html);
      }
    });
  });
}
