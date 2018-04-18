<?php
namespace iBrand\Wechat\Backend\Http\Controllers;

use iBrand\Wechat\Backend\Repository\MenuRepository;
use iBrand\Wechat\Backend\Facades\MenuService;
use Illuminate\Http\Request;
use iBrand\Wechat\Backend\Repository\MaterialRepository;

/**
 * 菜单管理.
 *
 */
class MenuController extends Controller
{

    protected $menuRepository;
    protected $materialRepository;


    public function __construct(MenuRepository $menuRepository,
                                MaterialRepository $materialRepository

    )
    {
        $this->menuRepository = $menuRepository;
        $this->materialRepository = $materialRepository;
    }

    /**
     * 菜单.
     */
    public function index()
    {

//        $menus=$this->menuRepository->getByAccountId(6);
//
//        return MenuService::saveToRemote($menus);


            $push_time=settings('wechat_push_menu_time');

            $menus=$this->menuRepository->getByAccountId(wechat_id());

            return view('Wechat::menu.index',compact('menus','push_time'));
    }


    public function create()
    {
        $pid=!empty(request('pid'))?request('pid'):0;

        if(!empty($pid)){
            $menusNumber=$this->menuRepository->getTwoMenuNumber(wechat_id(),$pid);
        }else{
            $menusNumber=$this->menuRepository->getFirstMenuNumber(wechat_id());
        }

        if($menusNumber<3&&$pid===0){
            return view('Wechat::menu.create');
        }

        if($menusNumber<5&&$pid!==0){
            return view('Wechat::menu.create');
        }

        flash('一级菜单最多3个，二级菜单最多5个', 'danger');

        return redirect()->back();

    }


    public function store(Request $request)
    {
        $data=$request->except('_token');
        $account_id=wechat_id();
        $pid=!empty(request('pid'))?request('pid'):0;

        if(!empty($pid)){
            $menusNumber=$this->menuRepository->getTwoMenuNumber(wechat_id(),$pid);
        }else{
            $menusNumber=$this->menuRepository->getFirstMenuNumber(wechat_id());
        }

        if($menusNumber>=3&&$pid===0){
            return $this->api(false,400,'一级菜单最多3个',[]);
        }

        if($menusNumber>=5&&$pid!==0){
            return $this->api(false,400,'二级菜单最多5个',[]);
        }

        $data=[
           'name'=>$data['name'],
            'parent_id'=>$pid,
            'type'=>$data['type'],
            'key'=>$data['key'],
            'sort'=>$data['sort'],
            'account_id'=>$account_id,
        ];

        if($data['type']==='media_id'){
            $media_id=$data['key'];
            $res=$this->materialRepository->find($media_id);
            $data['key']=$res->media_id;
        }

        $res=$this->menuRepository->create($data);

        return $this->api(true,200,'',$res);

    }


    public function edit($id)
    {

        $menu=$this->menuRepository->findByField('id',$id)->first();
        $material=[];
        $materials=[];

        if($menu->type==='media_id'){
            $materials=$this->materialRepository->findWhere(['media_id'=>$menu->key])->first();
        }

        if(count($materials)>0){
            $material['data_selected']=$materials->id;
            $material['data_type']=$materials->type;
            $material['data_img']=$materials->source_url;
            if($materials->type==="article"){
                $material['data_title']=$materials->title;
                $material['data_img']=$materials->cover_url;
                $material['data_time']=$materials->updated_at;
            }elseif ($materials->type==="video"){
                $material['data_title']=$materials->title;
            }

        }

        return view('Wechat::menu.edit',compact('menu','material'));
    }




    public function update(Request $request){
        $data=$request->except('_token');
        $account_id=wechat_id();

        $data=[
            'id'=>$data['id'],
            'name'=>$data['name'],
            'type'=>$data['type'],
            'key'=>$data['key'],
            'sort'=>$data['sort'],
        ];


        if($data['type']==='media_id'){
            $media_id=$data['key'];
            $res=$this->materialRepository->find($media_id);
            $data['key']=$res->media_id;
        }


        if($this->menuRepository->find($data['id'])->update($data)){
            return $this->api(true,200,'',[]);
        }
        return $this->api(false,400,'保存失败',[]);
    }



    public function destroy($id)
    {

        $account_id=wechat_id();
        $menus=$this->menuRepository->findWhere(['account_id'=>$account_id,'parent_id'=>$id]);
        if(count($menus)>0) {
            flash('含有二级级菜单删除失败', 'danger');
        }else{
            $this->menuRepository->delete($id);
            flash('删除成功', 'success');
        }

        return redirect()->back();
    }


    //发布菜单

    public function releaseMenu(){
        $app_id=wechat_app_id();
        $account_id=wechat_id();
        $menus=$this->menuRepository->getByAccountId($account_id);
        if(count($menus)>0){
            if(MenuService::saveToRemote($menus)){
                settings()->setSetting(['wechat_push_menu_time' =>time()]);
                return $this->api(true,200,'',[]);
            }else{
                return $this->api(false,400,'发布失败',[]);
            }
        }

        return $this->api(false,400,'请先添加菜单',[]);
    }


}
