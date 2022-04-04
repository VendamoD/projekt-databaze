<?php
require "includes/bootstrap.inc.php";

final class SamplePage extends BasePage {
    protected string $title = "Sample page";

    protected function body() : string
    {
        return "This is a demo page";
    }
}

(new SamplePage())->render();
