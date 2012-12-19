CI - PrinceXML
==============

A very lightweight library for creating PDF files with [PrinceXML](http://princexml.com)
via the PHP framework [CodeIgniter](http://codeigniter.com).

It's kept very lightweight with just the basic functionality.

Create a PDF from pure HTML
---------------------------

    $result = $this->prince->html_to_pdf('<h1>Beautiful</h1>');
    
    // Display in browser
    header('content-type: application/pdf');
    echo $result;
    
    // Or save to a file	
	file_put_contents('/path/to/file', $result);
	
Create a PDF from a view
-------------------------

    $result = $this->prince->view_to_pdf('create_me');

    // Display in browser
    header('content-type: application/pdf');
    echo $result;

    // Or save to a file	
	file_put_contents('/path/to/file', $result);
	
Create a PDF from a template
----------------------------

    $result = $this->prince->template_to_pdf('pdf_templates/create_me', array('foo' => 'bar));

    // Display in browser
    header('content-type: application/pdf');
    echo $result;

    // Or save to a file	
	file_put_contents('/path/to/file', $result);