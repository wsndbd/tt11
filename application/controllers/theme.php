<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Theme extends CI_Controller {

	/**
	 * 类目类的构造函数
	 *
	 * pagination库用来翻页
	 * M_item用来查询条目
	 */
	function __construct()
	{
		parent::__construct();
//		var_dump("Ee");
		$this->load->model('M_item');
		$this->load->model('M_theme');
		$this->load->model('M_cat');
		$this->load->model('M_keyword');
		$this->load->library('pagination');
	}

	/**
	 * 类目页
	 *
	 * @cat_slug string 类目slug，比如pants
	 *@offset integer 数据库偏移，如果是40则从40开始读取数据
	 *
	 */
	public function index($cat_slug,$page = 1)
	{
		//$this->output->cache(10);
		// todo 修改为页码数

		$this->config->load('site_info');
		$this->load->helper('subintercept');
		$limit=40;
		//每页显示数目
		$theme_slug_decode = rawurldecode($cat_slug);


		//初始化配置

		//通过数组传递参数

		//关键词列表，这个在后台配置
		$data['keyword_list'] = $this->M_keyword->get_all_keyword(5);

		$themeInfo =       $this->M_theme->get_theme_info($theme_slug_decode);
		//分类标题
//		$data['cat_name'] = $this->M_theme->get_theme_name($cat_slug_decode);
		$data['theme_name'] = $themeInfo->theme_name;
		$this->load->model('M_cat');
		$this->load->model('M_theme');
		$this->load->model('M_webtaobao');
		$data['cat']=$this->M_cat->get_all_cat();
		$data['theme']=$this->M_theme->get_all_theme();

		$data['theme_slug'] = $theme_slug_decode;
		//所有条目数据
//		$data['items']=$this->M_theme->get_all_item($limit,($page-1)*$limit,$cat_slug_decode);
		$data['items']=$this->M_webtaobao->getItemByTheme($page,$limit,
			$themeInfo->theme_relation);
//		var_dump($limit,($page-1)*$limit,
//					$themeInfo->theme_relation);
		//站点信息
		$data['site_name'] = $this->config->item('site_name');

		//keysords和description
		$data['site_keyword'] = $this->config->item('site_keyword');
		$data['site_description'] = $this->config->item('site_description');
		$config['base_url'] = site_url('/theme/'.$cat_slug);
		//site_url可以防止换域名代码错误。

		$config['total_rows'] = $data['items']->GetItemsByThemeCount();
		//这是模型里面的方法，获得总数。
		$config['use_page_numbers'] = true;
		$config['per_page'] = $limit;
		$config['first_link'] = '首页';
		$config['last_link'] = '尾页';
		$cionfig['num_links']=10;
//		$config['uri_segment'] = 3;
		//上面是自定义文字以及左右的连接数

		$this->pagination->initialize($config);
		$data['pagination']=$this->pagination->create_links();

		$this->load->view('theme_view',$data);

	}

	/**
	 * url转移
	 *
	 * 把/cat/pants/50这样的url转到index($cat_slug,$offset = 0)来处理
	 * 好处是只用创建一个function index就可以处理所有的类别/shirts或者/pants等等
	 *
	 * @slug String 类别slug，比如pants
	 * @params array 其他后续参数
	 */
	public function _remap($slug, $params = array())
	{
		//把$slug插入到$param后面，然后$param作为一个整体传递给index()调用
		array_unshift($params,$slug);

		return call_user_func_array(array('Theme', 'index'), $params);
	}





}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
