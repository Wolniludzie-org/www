<?php
class GlosowaniaOtwarte extends WP_List_Table {
	static $cache;    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'glosowanie',     //singular name of the listed records
            'plural'    => 'glosowania',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

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

			// logs
			//if( $has_logs = democr()->opt('keep_logs') && $wpdb->get_var("SELECT qid FROM $wpdb->democracy_log WHERE qid = ". intval($item->id) ." LIMIT 1") )
				//$actions[] = '<span class="edit">'.sprintf('<a href="?page=%s&action=%s&poll=%s">Szczegóły głosowania</a>',$_REQUEST['page'],'details',$item->id).'</span>';

			//return  democr()->kses_html( $item->question ) . "<div>$statuses</div>".'<div class="row-actions">'. implode(" ", $actions ) .'</div>';
			return  democr()->kses_html( $item->question ) . "<div>$statuses</div>";
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
		elseif( $column_name == 'added' ){
			$date = date( $date_format, $item->added );
			$end  = $item->end ? date( $date_format, $item->end ) : '';

			return "$date<br>$end";
		}
		else
			return isset( $item->$column_name ) ? $item->$column_name : print_r( $item, true );
	}	

	function get_columns(){
		$columns = array(
			'id'         => 'ID',
			'question'   => 'Pytanie',
			'in_posts'   => 'Głosuj na stronie',
			'added'      => 'Start/Stop',
		);
		return $columns;
	}	

	public function get_sortable_columns(){
		return array(
			'id'        => array('id','asc'),
			'question'  => array('question','asc'),
			'added'     => array('added','asc'),
		);
	}

    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        $per_page = 10;

/***************************************************************************************************************************************/
		$where   = 'WHERE 1';
		if( $s = @ $_GET['s'] ){
			$like = '%'. $wpdb->esc_like($s) .'%';
			$where .= $wpdb->prepare(" AND ( question LIKE %s OR id IN (SELECT qid from $wpdb->democracy_a WHERE answer LIKE %s) ) ", $like, $like );
		}
		$where.=' and open=1 ';

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

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        $current_page = $this->get_pagenum();
        
        $total_items = count($data);
        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}