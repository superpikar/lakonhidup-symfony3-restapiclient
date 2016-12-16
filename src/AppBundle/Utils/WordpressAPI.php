<?php
namespace AppBundle\Utils;

use Unirest;

class WordpressAPI
{
  const SITEURL = 'lakonhidup.wordpress.com';
  const APIURI = 'https://public-api.wordpress.com/rest/v1.1/sites/'.self::SITEURL;
  const HEADERS = array('Accept' => 'application/json');

  const NUMBEROFPOSTS = 10;
  const NUMBEROFTERMS = 999;

  public function getPosts($page=null)
  {
    $otherParams = !is_null($page)? array('page' => intval($page)) : array();
    return $this->getThePosts($otherParams);
  }

  public function getPostsByTaxonomyTerm($taxonomy, $term, $page=null)
  {
    $otherParams = array($taxonomy => $term);
    if(!is_null($page)){
      $otherParams = array_merge($otherParams, array('page' => intval($page)));
    }
    return $this->getThePosts($otherParams);
  }

  private function getThePosts($otherParams=[])
	{
    $query = array_merge($otherParams, array(
      'number' => self::NUMBEROFPOSTS,
      'fields' => 'ID,title,URL,slug,excerpt,tags,categories,attachments,discussion,like_count,meta'
    ));
    $response = Unirest\Request::get(self::APIURI.'/posts',self::HEADERS,$query);
    foreach ($response->body->posts as $key => $value) {
      $value = $this->setPost($value);
    }
    return $response;
	}

  public function getPost($slug)
  {
    $query = array(
      'fields' => 'ID,title,URL,slug,excerpt,content,tags,categories,attachments,discussion,like_count,meta'
    );
    $response = Unirest\Request::get(self::APIURI.'/posts/slug:'.$slug,self::HEADERS,$query);
    $response->body = $this->setPost($response->body);
    return $response;
  }

  public function getPostComments($postID)
  {
    $query = array(
      'fields' => 'ID,author,date,content,like_count'
    );
    $response = Unirest\Request::get(self::APIURI.'/posts/'.$postID.'/replies',self::HEADERS,$query);
    return $response;
  }

  public function getTermsByTaxonomy($taxonomy)
	{
    $query = array('number' => self::NUMBEROFTERMS);
    $taxonomy = $this->mappingTaxonomy($taxonomy);
    $response = Unirest\Request::get(self::APIURI.'/'.$taxonomy,self::HEADERS,$query);
    return $response;
	}

  public function getTermByTaxonomy($taxonomy, $slug)
	{
    $taxonomy = $this->mappingTaxonomy($taxonomy);
    $response = Unirest\Request::get(self::APIURI.'/'.$taxonomy.'/slug:'.$slug,self::HEADERS);
    return $response;
	}

  private function mappingTaxonomy($taxonomy) {
    switch ($taxonomy) {
      case 'tag':
        $taxonomy = 'tags';
        break;
      case 'category':
      default:
        $taxonomy = 'categories';
        break;
    }
    return $taxonomy;
  }

  private function setPost($post)
  {
    // $value->excerpt = 'Cerpen Adam Yudhistira (Media Indonesia, 13 November 2016) bla bla bla'
    $opening = explode(')', strip_tags($post->excerpt));
    $opening2 = explode(',',  $opening[0]);
    $opening3 = explode('(',  $opening2[0]);
    $post->summary = trim($opening[1]);
    $post->publishedDate = trim($opening2[1]);
    // $post->authorName = trim(str_replace('Cerpen', '', $opening3[0]));
    // $post->mediaName = trim($opening3[1]);

    /* SET THUMBNAIL */
    // if not empty array, credit : http://stackoverflow.com/questions/9412126/how-to-check-that-an-object-is-empty-in-php
    if(!(array)$post->attachments){
      // if doesnt have attachment, then set null
      $post->thumbnailImage = null;
      $post->coverImage = null;
    }
    else{
      foreach ($post->attachments as $key2 => $value2) {
        if((array)$value2->thumbnails){
          $post->thumbnailImage = $value2->thumbnails->medium;
        }
        else{
          $post->thumbnailImage = $value2->URL;
        }
        $post->coverImage = $value2->URL;
      }
    }

    /* SET AUTHOR INFO */
    foreach ($post->categories as $key2 => $value2) {
      $post->authorName = $key2;   // set author name from categories
      $post->authorSlug = $value2->slug;   // set author name from categories
    }

    /* SET MEDIA INFO */
    foreach ($post->tags as $key2 => $value2) {
      $post->mediaName = $key2;   // set author name from tags
      $post->mediaSlug = $value2->slug;   // set author name from tags
    }
    return $post;
  }
}
