<?php

namespace App\Admin\Controllers;

use App\Models\Categorie;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

class CategorieController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '栏目';
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->title($this->title)
            ->body($this->tree());
    }
    /**
     * Make a grid builder.
     *
     * @return Tree
     */
    protected function tree()
    {
        return Categorie::tree(function (Tree $tree) {

            $tree->branch(function ($branch) {
                if($branch['mid'] == 2)
                    $midname =  "单页面";
                else if($branch['mid'] == 3)
                     $midname =  "项目";
                else if($branch['mid'] == 4)
                     $midname =  "问答";
                else if($branch['mid'] == 5)
                     $midname =  "商城";
                else if($branch['mid'] == 6)
                     $midname =  "供应"; 
                else if($branch['mid'] == 7)
                     $midname =  "图库";                
                else
                    $midname = "文章";

                // $typename ='<a href="{$typedir}" target="_blank">{$tname}</a>';

                return "{$branch['id']} - {$branch['typename']} -[{$midname}]";

            });
        });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid // 非树形结构栏目列表
     */
    protected function grid()
    {
        $grid = new Grid(new Categorie);

        $grid->column('id', __('Id'));
        $grid->column('parent_id', __('父栏目'))->display(function ($parent_id) {
            $cates = Categorie::where('id',$parent_id)->first();
            if($cates)
                return $cates->typename;
            else
                return "无";
        });
        $grid->column('typename', __('栏目名'));
        $grid->column('typedir', __('栏目url'));
        $grid->column('mid', __('父栏目'))->display(function ($mid) {
            if($mid == 2)
                return "单页面";
            else if($mid == 3)
                return "项目";
            else if($mid == 4)
                return "问答";
            else if($mid == 5)
                return "商城";
            else if($mid == 6)
                return "供应";
            else if($mid == 7)
                return "图库";
            else
                return "文章";
        });
        $grid->column('created_at', __('添加时间'))->hide();
        // $grid->column('updated_at', __('Updated at'));

        $grid->filter(function($filter){
             $filter->disableIdFilter();
            // 在这里添加字段过滤器 列表搜索
            $filter->like('typename', '栏目名');
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Categorie::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('topid', __('Topid'));
        $show->field('sortrank', __('Sortrank'));
        $show->field('typename', __('Typename'));
        $show->field('typedir', __('Typedir'));
        $show->field('title', __('Title'));
        $show->field('keyword', __('Keyword'));
        $show->field('dirposition', __('Dirposition'));
        $show->field('contents', __('Contents'));
        $show->field('mid', __('Mid'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Categorie);

         $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        // $form->select('parent_id','父类栏目')->options('/admin/categories');

        $form->tab('常规选项', function ($form) {

            // $form->radio('parent_id', '相对位置')->options(['1' => '站点根目录', '2'=> '上级目录'])->default('1');
            // $form->select('parent_id','父栏目【顶级栏目可不选】')->options(function () {
            //     $cates = Categorie::where('status',1)->get();
                
            //     if ($cates) {
            //         foreach ($cates as $v) {
            //             return [$v->id => $v->typename];
            //         }
                    
            //     }
            // });

            $form->select('parent_id','父栏目')->options(Categorie::selectOptions());
            $form->text('typename', __('栏目名'))->required();
            $form->text('typedir', __('栏目url'))->required();
            $form->number('order', __('排序'))->default(1);
            $form->text('title', __('标题'))->required();;
            $form->text('keyword', __('关键词'));
            $form->text('dirposition', __('描述'));
            // $form->number('mid', __('栏目类型'))->default(1);
            $form->radio('mid', '栏目类型')->options(['1' => '普通文章', '2'=> '单页面', '3'=> '项目类型', '4'=> '问答', '5'=> '商城', '6'=> '供应', '7'=> '图库'])->default('1');
            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ];

            $form->switch('status','状态')->states($states)->default('1');

        })->tab('单页面内容填写', function ($form) {

           // $form->textarea('contents', __('单页面内容'))->placeholder('栏目类型选单页面时在填写此项');
            $form->ueditor('content');

        });

        return $form;
    }
}
