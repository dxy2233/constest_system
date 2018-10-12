<?php
namespace common\components;
/**
 * 分页类文件
 * @author yaopeng
 */

class PageList{
    //总记录数
    private $totalRow;
    //总页数
    private $totalPage;
    //每页的显示的条数
    private $perPage;
    //上一页 替换 %href%
    private $prevPage = '<li><a href="%href%">上一页</a></li>';
    //下一页 替换 %href%
    private $nextPage = '<li><a href="%href%">下一页</a></li>';
    //当前页
    private $currentPage;
    //页码的样式模板 替换 %href%(连接)  %page%
    private $pageClass = '<li><a href="%href%">%page%</a></li>';
    //当前页的样式 %href%(连接)  %page% (当前页)
    private $currentPageStyle = '<li class="active"><span>%page%</span></li>';
    //显示的条数
    private $showNum = 7;
    //第一页
    private $firstPage;
    //最后一页
    private $endPage;
    //url 模板 替换 %page%
    private $formatUrl;
    //$_GET中的值 默认为 $_GET['page']
    private $pageKey = 'p';
    //省略的模板， 默认是...
    private $cut = '<span>...</span>';

    /**
     *
     * @param array $config  config 必须配置的key有
     *
     *
     */
    public function __construct($config) {
        $this->parseConfig($config);
        //为属性赋值
        $this->formatUrl = urldecode($this->formatUrl);
        $this->totalPage = ceil($this->totalRow / $this->perPage);
        $this->currentPage = empty($this->currentPage) ? (empty($_GET[$this->pageKey]) ? 1 : intval($_GET[$this->pageKey])) : $this->currentPage;
        $this->currentPage = $this->currentPage > $this->totalPage ? $this->totalPage : ($this->currentPage < 1 ? 1 : $this->currentPage);
        $this->currentPage = $this->currentPage < 1 ? 1 : $this->currentPage;
        $this->firstPage = $this->getOnePage(1);
        $this->endPage = $this->getOnePage($this->totalPage);
        $this->prevPage = str_replace('%href%', $this->getHref($this->currentPage-1), $this->prevPage);
        $this->nextPage = str_replace('%href%', $this->getHref($this->currentPage+1), $this->nextPage);
    }

    private function parseConfig($config){
        foreach ($config as $k => $v){
            if(property_exists($this, $k)){
                $this->$k = $v;
            }
        }
    }
    /**
     * 获取第几页的url连接
     * @param type $page
     */
    private function getHref($page){
        $url = '';
        if(strpos($this->formatUrl, '%page%')) {
            $url = str_replace('%page%',$page,  $this->formatUrl);
        }else {
            $url = strpos($this->formatUrl, '?') ? $this->formatUrl.'&'.$this->pageKey.'='.$page : $this->formatUrl.'?'.$this->pageKey.'='.$page;
        }
        return $url;
    }
    /**
     * 获取一个一页的内容
     * @param type $page
     */
    private function getOnePage($page){
        if($page == $this->currentPage) {
            $onePage = str_replace('%page%', $page, $this->currentPageStyle);
            $onePage = str_replace('%href%', $this->getHref($page), $onePage);
            return $onePage;
        }
        $onePage = str_replace('%page%', $page, $this->pageClass);
        $onePage = str_replace('%href%', $this->getHref($page), $onePage);
        return $onePage;
    }

    /**
     * 获取分页列表
     */
    public function getPage(){
        if ($this->totalPage <=1 ) return '';
        $page = ($this->currentPage == 1) ? '' : $this->prevPage;//生成上一页
        $page .= $this->firstPage;
        $stoppage = 0;
        //判断是否显示 cut
        if($this->currentPage > ($this->showNum -2)){
            $page .= $this->cut;
            if($this->currentPage > ($this->totalPage - ($this->showNum -2))){
                for($i=($this->totalPage - ($this->showNum -2));$i<=$this->totalPage;$i++){
                    $page.=$this->getOnePage($i);
                }
                $stoppage = $this->totalPage;
            }else{
                //中间的情况
                $begin = $this->currentPage - ($this->showNum-3) /2;
                $end = $this->currentPage + ($this->showNum-3) /2;
                $end = $end > $this->totalPage ? $this->totalPage : $end;
                for($i=$begin;$i<=$end;$i++){
                    $page.=$this->getOnePage($i);
                }
                $stoppage = $end;
            }
        }else{
            for($i=2;$i<$this->showNum && $i <= $this->totalPage;$i++){
                $page.=$this->getOnePage($i);
            }
            $stoppage = $i;
        }
        if($stoppage < $this->totalPage){
            $page.= $this->cut.$this->endPage;
        }
        if($this->currentPage < $this->totalPage){
            $page.=$this->nextPage;
        }
        return $page;
    }

    public function getCurrentPage(){
        return $this->currentPage;
    }
}
