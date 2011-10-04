<?php
class PR_Page extends Page
{
	function PagePreInclude()
	{
	}
}

$page = Site::LinkIncludePage( new PR_Page() );
?>