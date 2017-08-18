#!/usr/bin/env bash
echo "开始同步分支代码到本地，完成后请解决冲突"
echo "开始同步分支代码到本地，完成后请解决冲突"
echo "开始同步分支代码到本地，完成后请解决冲突"
svn up --accept postpone
echo "请确保本地所有修改已经提交"
echo "请确保本地所有修改已经提交"
echo "请确保本地所有修改已经提交"

echo "!!!!!!!!!!!!!!!!!!!!!"
echo "!!!!!!!!!!!!!!!!!!!!!"
echo "!!!!!!!!!!!!!!!!!!!!!"
read -r -p "确认了吗，小伙子，本地所有修改都提交了? [y/n] " input
case $input in
    [yY][eE][sS]|[yY])
        username=`svn info|grep 'URL: svn://'| cut -d '@' -f 1 | cut -d '/' -f 3`
        svn merge --reintegrate --accept postpone svn://$username@120.24.217.103/repository/outsource_projects/wu_xb/wechat_app_admin_dev_branch
        echo "同步完成 请到IDE左下角--->Version Control--->Local Changes 解决冲突然后提交到主干上"
        echo "同步完成 请到IDE左下角--->Version Control--->Local Changes 解决冲突然后提交到主干上"
        echo "同步完成 请到IDE左下角--->Version Control--->Local Changes 解决冲突然后提交到主干上"
		;;
    [nN][oO]|[nN])
		echo "再见！！"
       	;;
    *)
	echo "参数不对，再见！！"
	exit -1
	;;
esac

