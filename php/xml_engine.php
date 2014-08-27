<?php

class xml_engine {
	
	private $doc;
	
	private $selected_element;
	
	
	public final function xml_engine( $base_filename )
	{
		$this->doc	= file_get_contents( "../html/base/".$base_filename );
	}
	
	/**
	 * Load the html-file for the requested URL
	 * <hr />
	 * @param string $filename
	 */
	public function load( $filename )
	{
		if ( !file_exists( "../html/sites/".$filename ) || $filename == "" )
			$filename = "dev.html";
		
		if ( $_SESSION["logged_in"] ) {
			$name = "in";
		} else {
			$name = "out";
		}
		
		$this->doc	= str_replace( "{top_bar}", file_get_contents( "../html/base/logged_".$name."/top_bar.html" ), $this->doc );
		$this->doc	= str_replace( "{nav_bar}", file_get_contents( "../html/base/logged_".$name."/nav_bar.html" ), $this->doc );
		$this->doc	= str_replace( "{chat_bar}", file_get_contents( "../html/base/logged_".$name."/chat_bar.html" ), $this->doc );
		
		$this->doc	= str_replace( "{content}", file_get_contents( "../html/sites/".$filename ), $this->doc );
		$this->doc	= new SimpleXMLElement( $this->doc );
	}
	
	
	/* SELECT Methods */
	
	/**
	 * Select an element
	 * <hr />
	 * @param string $element
	 * @return xml_engine
	 */
	public function select_element( $element )
	{
		$this->selected_element = $this->doc->xpath( "//*[name() = \"".$element."\"]" );
		return $this;
	}
	
	/**
	 * Select an element by text
	 * <hr />
	 * @param string $text
	 * @return xml_engine
	 */
	public function select_text( $text )
	{
		$this->selected_element = @$this->doc->xpath( "//*[text() = \"".$text."\"]" );
		return $this;
	}
	
	/**
	 * Select an element by id
	 * <hr />
	 * @param string $id
	 * @return xml_engine
	 */
	public function select_id( $id )
	{
		$this->selected_element = @$this->doc->xpath( "//*[@id=\"".$id."\"]" );
		return $this;
	}
	
	/**
	 * Select elements by class
	 * <hr />
	 * @param string $class
	 * @return xml_engine
	 */
	public function select_class( $class )
	{
		$this->selected_element = @$this->doc->xpath( "//*[@class=\"".$class."\"]" );
		return $this;
	}
	
	/**
	 * Select elements with by attribute
	 * <hr />
	 * @param string $attr
	 * @param string $value
	 * @return xml_engine
	 */
	public function select_attr( $attr, $value )
	{
		$this->selected_element = @$this->doc->xpath( "//*[@".$attr."=\"".$value."\"]" );
		return $this;
	}
	
	
	
	
	/* GET Methods */
	
	/**
	 * Read the text of an element
	 * <hr />
	 * @return string $text
	 */
	public function get_text()
	{
		return @$this->selected_element[0][0];
	}
	
	/**
	 * Read the value of an attribute
	 * <hr />
	 * @param string $attr
	 * @return string
	 */
	public function get_attr( $attr )
	{
		return @$this->selected_element[0][$attr];
	}
	
	
	
	
	/* SET Methods */
	
	/**
	 * Set the value of an attribute
	 * <hr />
	 * @param string $attr
	 * @param string $value
	 * @return xml_engine
	 */
	public function set_attr( $attr, $value = NULL )
	{
		foreach ( (array) $this->selected_element as $element ) {
			@$element[$attr] = $value;
		}
		return $this;
	}
	
	/**
	 * Set the text of an element
	 * <hr />
	 * @param string $text
	 * @return xml_engine
	 */
	public function set_text( $text )
	{
		foreach ( (array) $this->selected_element as $element ) {
			@$element[0] = $text;
		}
		return $this;
	}
	
	/**
	 * Set the HTML contents of an element
	 * <hr />
	 * @param string $text
	 * @return xml_engine
	 */
	public function set_html( $text )
	{
		foreach ( (array) $this->selected_element as $element ) {
			@$element[0] = '\['.$text.'\]';
		}
		return $this;
	}
	
	
	
	/* ADD Methods */
	
	/**
	 * Add a CSS stylesheet to the page
	 * <hr />
	 * @param string $href
	 * @return xml_engine
	 */
	public function add_css( $href )
	{
		$new_element = $this->doc->head->addChild( "link" );
		$new_element->addAttribute( "rel", "stylesheet" );
		$new_element->addAttribute( "href", $href );
		$new_element->addAttribute( "media", "screen" );
		return $this;
	}
	
	/**
	 * Add a javascript file to the page
	 * <hr />
	 * @param string $src
	 * @return xml_engine
	 */
	public function add_js( $src )
	{
		$new_element = $this->doc->head->addChild( "script", " " );
		$new_element->addAttribute( "src", $src );
		return $this;
	}
	
	/**
	 * Add Childs to the selected element
	 * <hr />
	 * @param string $name
	 * @param string $value
	 */
	public function add_child( $name, $value = NULL )
	{
		foreach ( (array) $this->selected_element as $element ) {
			$element->addChild( $name, $value );
		}
	}
	
	public function add_attr( $name, $value = NULL )
	{
		foreach ( (array) $this->selected_element as $element ) {
			$element->addAttribute( $name, $value );
		}
	}
	
	
	/* Misc. */
	
	/**
	 * Use the raw SimpleXML-Library
	 * <hr />
	 * @return SimpleXMLElement
	 */
	public function raw_mode()
	{
		return $this->doc;
	}
	
	/**
	 * Set the title of the page
	 * <hr />
	 * @param string $title
	 * @return xml_engine
	 */
	public function set_title( $title )
	{
		$this->doc->head->title = $title;
		return $this;
	}
	
	/**
	 * Replace HTML-Variables with values
	 * <hr />
	 * @param string $key
	 * @param string $value
	 * @return xml_engine
	 */
	public function assign( $key, $value )
	{
		$this->select_text( '{'.$key.'}' )->set_text( $value );
		return $this;
	}
	
	/**
	 * Print the page
	 * <hr />
	 * @return string
	 */
	public function print_page()
	{
		$buffer		= $this->doc->asXML();
		preg_match_all( "/\\\[(.*)\\\]/", $buffer, $results );
		foreach ( $results[1] as $value ) {
			$buffer = preg_replace( "/\\\[(.*)\\\]/", html_entity_decode( $value ), $buffer, 1 );
		}
		
		return $buffer;
		
	}
};
