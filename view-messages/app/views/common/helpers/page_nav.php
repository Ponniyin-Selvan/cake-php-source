<?php
class PageNavHelper extends PaginatorHelper {

	var $helpers = array('Html');

    //function to return the pagination string
	function getPaginationString($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
	{
		//defaults
		if(!$adjacents) $adjacents = 1;
		if(!$limit) $limit = 15;
		if(!$page) $page = 1;
		if(!$targetpage) $targetpage = "/";

		//other vars
		$prev = $page - 1;									//previous page is page - 1
		$next = $page + 1;									//next page is page + 1
		$lastpage = ceil($totalitems / $limit);				//lastpage is = total items / items per page, rounded up.
		$lpm1 = $lastpage - 1;								//last page minus 1

		/*
			Now we apply our rules and draw the pagination object.
			We're actually saving the code to a variable in case we want to draw it more than once.
		*/
		$pagination = "";
		if($lastpage > 1)
		{
			//previous button
			if ($page > 1)
				$pagination .= "<a title=\"Go to Previous Page\" href=\"$targetpage$pagestring$prev\">« prev</a>";
			else
				$pagination .= "<span class=\"disabled\">« prev</span>";

			//pages
			if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
			{
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a title=\"Go to Page $counter\" href=\"$targetpage$pagestring$counter\">$counter</a>";
				}
			}
			elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
			{
				//close to beginning; only hide later pages
				if($page < 1 + ($adjacents * 3))
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page)
							$pagination .= "<span class=\"current\">$counter</span>";
						else
							$pagination .= "<a title=\"Go to Page $counter\" href=\"$targetpage$pagestring$counter\">$counter</a>";
					}
					$pagination .= "...";
					$pagination .= "<a title=\"Go to Page $lpm1\" href=\"$targetpage$pagestring$lpm1\">$lpm1</a>";
					$pagination .= "<a title=\"Go to Page $lastpage\" href=\"$targetpage$pagestring$lastpage\">$lastpage</a>";
				}
				//in middle; hide some front and some back
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination .= "<a title=\"Go to Page 1\" href=\"$targetpage${pagestring}1\">1</a>";
					$pagination .= "<a title=\"Go to Page 2\" href=\"$targetpage${pagestring}2\">2</a>";
					$pagination .= "...";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination .= "<span class=\"current\">$counter</span>";
						else
							$pagination .= "<a title=\"Go to Page $counter\" href=\"$targetpage$pagestring$counter\">$counter</a>";
					}
					$pagination .= "...";
					$pagination .= "<a title=\"Go to Page $lpm1\" href=\"$targetpage$pagestring$lpm1\">$lpm1</a>";
					$pagination .= "<a title=\"Go to Page $lastpage\" href=\"$targetpage$pagestring$lastpage\">$lastpage</a>";
				}
				//close to end; only hide early pages
				else
				{
					$pagination .= "<a title=\"Go to Page 1\" href=\"$targetpage${pagestring}1\">1</a>";
					$pagination .= "<a title=\"Go to Page 2\" href=\"$targetpage${pagestring}2\">2</a>";
					$pagination .= "...";
					for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination .= "<span class=\"current\">$counter</span>";
						else
							$pagination .= "<a title=\"Go to Page $counter\" href=\"$targetpage$pagestring$counter\">$counter</a>";
					}
				}
			}

			//next button
			if ($page < $counter - 1)
				$pagination .= "<a title=\"Go to Next Page\" href=\"$targetpage$pagestring$next\">next »</a>";
			else
				$pagination .= "<span class=\"disabled\">next »</span>";
		}

		return $pagination;

	}

	function display() {
    	// pageno, 15, 10, /messages, "page:
    	$pageNo = null;

    	if (isset($this->params['pass']['page'])) {
    		$pageNo = $this->params['pass']['page'];
    	}
    	$pageCount = $this->params['paging']['Post']['pageCount'];
		echo $this->getPaginationString($pageNo, $pageCount * 15, 15, 2, "/messages/index/", "page:");
    }

	function display2($page_no, $page_count) {
		echo $this->getPaginationString($page_no, $page_count * 15, 15, 2, "/messages/index/", "page:");
    }
}
?>