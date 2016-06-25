<?php

/**
    专业生成分页工具条的html
 */
class PageTool
{
    /**
     * 专业生成分页工具条的html
     * @param $total   总条数
     * @param $total_page  总页数
     * @param $page  当前页
     * @return string  分页的html代码
     */
    public static function show($url,$total,$total_page,$page){
        $pre_page = $page==1?1:$page-1;  //计算出上一页
        $next_page = $page==$total_page?$total_page:$page+1;  //计算出上一页


        //用来生成分页的下拉框
        $optionHtml = "";
        for($i=1;$i<=$total_page;++$i){
            //选中默认的页码
            $selected='';
            if($i==$page){
                $selected='selected';
            }
            $optionHtml.="<option {$selected} value='{$i}'>{$i}</option>";
        }

        $pageToolHtml = <<<XXXX
<div id="turn-page" style="text-align: right">
            总计 <span id="totalRecords">{$total}</span>
            个记录分为 <span id="totalPages">{$total_page}</span>
            页当前第 <span id="pageCurrent">{$page}</span>
            页
                        <span id="page-link">
                            <a href="{$url}&page=1">第一页</a>
                            <a href="{$url}&page={$pre_page}">上一页</a>
                            <a href="{$url}&page={$next_page}">下一页</a>
                            <a href="{$url}&page={$total_page}">最末页</a>
                            <select id="gotoPage">
                               {$optionHtml}
                            </select>
                        </span>
        </div>
        <script type="text/javascript">
            //>>1.得到select标签
            var gotoPage = document.getElementById('gotoPage');
            //>>2.在select标签上添加change事件
                gotoPage.onchange = function(){
                    //this代表当前select标签对象
                   location.href = '{$url}&page='+this.value;
                }
        </script>
XXXX;

        return $pageToolHtml;
    }
}