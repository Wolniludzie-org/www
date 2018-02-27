<?php

class GlosowaniaZakonczone extends WP_List_Table {
	static $cache;    
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'glosowanie',     //singular name of the listed records
            'plural'    => 'glosowania',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
	/*
    function column_default($item, $column_name){
        switch($column_name){
            case 'id':
            case 'question':
            case 'open':
            case 'active':
            case 'usersvotes':
            case 'answers':
            case 'in_posts':
            case 'added':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
	*/
	public function column_default( $item, $column_name ){
		global $wpdb;

		$cache = & self::$cache;
		if( ! isset( $cache[ $item->id ] ) )
			$cache[ $item->id ] = $wpdb->get_results("SELECT * FROM $wpdb->democracy_a WHERE qid = ". intval($item->id) );

		$answ = & $cache[ $item->id ];
		
		$admurl = democr()->admin_page_url();
		$date_format = get_option('date_format');

		if(0){}
		elseif( $column_name == 'question' ){
			$statuses =
			'<span class="statuses">'.
				($item->democratic   ? '<span class="dashicons dashicons-megaphone" title="'. __('Users can add answers (democracy).','democracy-poll') .'"></span>'           : '').
				($item->revote       ? '<span class="dashicons dashicons-update" title="'. __('Users can revote','democracy-poll') .'"></span>'                                : '').
				($item->forusers     ? '<span class="dashicons dashicons-admin-users" title="'. __('Only for registered user.','democracy-poll') .'"></span>'                  : '').
				($item->multiple     ? '<span class="dashicons dashicons-image-filter" title="'. __('Users can choose many answers (multiple).','democracy-poll') .'"></span>' : '').
				($item->show_results ? '<span class="dashicons dashicons-visibility" title="'. __('Allow to watch the results of the poll.','democracy-poll') .'"></span>'     : '').
			'</span>';

			// actions
			$actions = array();
			// user can edit

				// edit
				//$actions[] = '<span class="edit"><a href="'. democr()->edit_poll_url( $item->id ) .'">'. __('Edit','democracy-poll') .'</a> | </span>';

				// logs
				if( $has_logs = democr()->opt('keep_logs') && $wpdb->get_var("SELECT qid FROM $wpdb->democracy_log WHERE qid = ". intval($item->id) ." LIMIT 1") )
					/*$actions[] = '<span class="edit"><a href="'. add_query_arg( array('subpage'=>'logs', 'poll'=> $item->id), $admurl ) .'">Szczegóły głosowania</a> </span>';*/
					$actions[] = '<span class="edit">'.sprintf('<a href="?page=%s&action=%s&poll=%s">Szczegóły głosowania</a>',$_REQUEST['page'],'details',$item->id).'</span>';

				// delete
				//$actions[] = '<span class="delete"><a href="'. add_query_arg( array('delete_poll'=> $item->id), $admurl ) .'" onclick="return confirm(\''. __('Are you sure?','democracy-poll') .'\');">'. __('Delete','democracy-poll') .'</a> | </span>';


			// shortcode
			//$actions[] = '<span style="color:#999">'. DemPoll::shortcode_html( $item->id ) .'</span>';

			return  democr()->kses_html( $item->question ) . "<div>$statuses</div>".'<div class="row-actions">'. implode(" ", $actions ) .'</div>';
		}
		elseif( $column_name == 'usersvotes' ){
			$votes_sum = array_sum( wp_list_pluck( (array) $answ, 'votes' ) );
			return $item->multiple ? '<span title="'. __('voters / votes','democracy-poll') .'">'. $item->users_voted .' <small>/ '. $votes_sum .'</small></span>' : $votes_sum;
		}
		elseif( $column_name == 'in_posts' ){
			if( ! $posts = democr()->get_in_posts_posts( $item ) )
				return '';

			$out = array();

			$__substr = function_exists('mb_substr') ? 'mb_substr' : 'substr';
			foreach( $posts as $post )
				$out[] = '<a href="'. get_permalink($post) .'">'. $__substr( $post->post_title, 0, 80 ) .' ...</a>';

			$_style = ' style="margin-bottom:0; line-height:1.4;"';

			return ( count($out) > 1 ) ?
				'<ol class="in__posts" style="margin:0 0 0 1em;"><li'.$_style.'>'. implode('</li><li'.$_style.'>', $out ) .'</li></ol>' :
				$out[0];

		}
		elseif( $column_name == 'answers' ){
			if( ! $answ )
				return 'Brak';

			usort( $answ, function( $a, $b ){
				return $a->votes == $b->votes ? 0 : ( $a->votes < $b->votes ? 1 : -1 );
			} );

			$_answ = array();
			foreach( $answ as $ans ){
				
				//print_r($ans);
				if ($ans->votes) {
					$_answ[] = sprintf('<a href="?page=%s&action=%s&poll=%s&answer=%s"><small>%s</small> %s</a>',$_REQUEST['page'],'usersvoted',$item->id,$ans->aid,$ans->votes,$ans->answer);
				}
				else {
					$_answ[] = '<small>'. $ans->votes .'</small> '. $ans->answer;
				}
			}
			return '<div class="compact-answ">'. implode('<br>', $_answ ) .'</div>';
		}
		elseif( $column_name == 'active' ){
			//return democr()->cuser_can_edit_poll($item) ? dem_activatation_buttons( $item, 'reverse' ) : '';
			return $item->$column_name;
		}
		elseif( $column_name == 'open' ){
			//return democr()->cuser_can_edit_poll($item) ? dem_opening_buttons( $item, 'reverse' ) : '';
			return $item->$column_name;
			
		}
		elseif( $column_name == 'added' ){
			$date = date( 'Y.m.d', $item->added );
			$end  = $item->end ? date( 'Y.m.d', $item->end ) : '';

			return "$date<br>$end";
		}
		else
			return isset( $item->$column_name ) ? $item->$column_name : print_r( $item, true );
	}	




    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    /*function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Title',
            'rating'    => 'Rating',
            'director'  => 'Director'
        );
        return $columns;
    }*/
	function get_columns(){
		$columns = array(
			'id'         => 'ID',
			'question'   => 'Pytanie',
			/*'open'       => 'Poll Opened',*/
			/*'active'     => 'Active polls',*/
			'usersvotes' => 'Głosy',
			'answers'    => 'Odpowiedzi',
			'in_posts'   => 'Wyniki (graf.)',
			'added'      => 'Start/Stop',
		);
		return $columns;
	}	

    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
	public function get_sortable_columns(){
		return array(
			'id'        => array('id','asc'),
			'question'  => array('question','asc'),
			'usersvotes'=> array('users_voted','asc'),
			'added'     => array('added','asc'),
		);
	}



    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 10;

/***************************************************************************************************************************************/
		$where   = 'WHERE 1';
		if( $s = @ $_GET['s'] ){
			$like = '%'. $wpdb->esc_like($s) .'%';
			$where .= $wpdb->prepare(" AND ( question LIKE %s OR id IN (SELECT qid from $wpdb->democracy_a WHERE answer LIKE %s) ) ", $like, $like );
		}
		$where.=' and open=0 ';

		$this->set_pagination_args( array(
			'total_items' => $wpdb->get_var("SELECT count(*) FROM $wpdb->democracy_q $where"),
			'per_page'    => $per_page,
		) );
		$cur_page = (int) $this->get_pagenum(); // после set_pagination_args()


		$OFFSET  = 'LIMIT '. (($cur_page-1) * $per_page .','. $per_page );
		$order   = @ $_GET['order']=='asc' ? 'ASC' : 'DESC';
		$orderby = @ $_GET['orderby'] ?: 'id';
		$ORDER_BY = sprintf("ORDER BY %s %s", sanitize_key($orderby), $order );

		$sql = "SELECT * FROM $wpdb->democracy_q $where $ORDER_BY $OFFSET";

		$data = $wpdb->get_results( $sql);
/***************************************************************************************************************************************/
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        //$data = $this->example_data;
                
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
		 /*
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        */
        
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
        
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }


}