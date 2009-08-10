***************************************************************************
	CakePHP1.2＋Smarty用View「SmartyView」説明書
	Copyright 2008-2009 ECWorks ( http://www.ecworks.jp/ )
***************************************************************************

　ダウンロードいただきましてありがとうございます。

　本ドキュメントでは、SmartyViewの設置方法および設定方法について簡単に
ご説明させていただきます。設置する前にご一読いただきますよう、お願い
申し上げます。

--------------------------------------------------
■はじめに
--------------------------------------------------

　本Viewクラスは、CakePHPにてSmartyテンプレートエンジンを駆動させるための
Viewを提供します。Smartyを用いた既存システムからの移植が容易になる他、純正
Viewと違いソースコードをデザイナに渡す必要がないため、作業分担が明確になる
メリットがあります。また、テンプレートをコンパイルしたものを保存するため
２回目以降が高速に駆動しますし、キャッシュ機能も搭載しています。Smartyに
関しては、 http://www.smarty.net/ をご覧ください。

　実はSmarty用Viewは、本家Bakeryに紹介されているのですが、既に古い仕様となって
いるため、新しく作る必要がありました。そこで、最新のview.phpをベースに、
新たに作り直しました。このためBekrty版とは若干仕様が異なっていますので、導入の
際にはお気を付けください。

--------------------------------------------------
■動作環境
--------------------------------------------------

　本Viewクラスは、CakePHP1.2.4.8284 Stableからリライトして作成しています。
1.2.0以降、いくつか該当メソッドに改良があるようですが、大きな変更はありません
ので、おそらくStableでしたら動作可能かと思います。

　導入に先立って、Smartyシステム一式も別途必要になります。
http://www.smarty.net/ から最新バージョンをダウンロードしておいてください。
動作確認はバージョン2.6.22にて行っております。

--------------------------------------------------
■ご利用条件
--------------------------------------------------

　SmartyViewクラスおよび添付ファイル一式は、MITライセンスに準拠いたします。
The SmartyView's source code and attach files are distributed under THE MIT 
LICENSE.

--------------------------------------------------
■必要ファイルとアップロード方法
--------------------------------------------------

　配布アーカイブを解凍すると、次のファイルが生成されます。生成ファイルを、
CakePHP内の所定の場所にアップロードしてください。emptyファイルはダミー
ファイルのため、アップロードは不要です。また、viewsディレクトリ内にある
mypagesディレクトリ以下、およびcontrollersディレクトリ内の
mypages_controllers.phpはサンプルですので、不要でしたらアップロード
しなくても大丈夫です。

　[]内は、パーミッション設定の値です。一般的な値が記載されていますが
サーバーによって異なりますので適宜修正してください。特にtmpディレクトリ
以下はPHP側からの書き込みが行われますので、書き込みの出来る設定を
してください。

-+- cake								[***] 
 |  +- app								[755] 
 |  |  +- controllers					[755] 
 |  |  |  +- mypages_controllers.php	[644] サンプルコントローラ
 |  |  +- tmp							[777] 
 |  |  |  +- smarty						[777] 
 |  |  |     +- cache					[777] キャッシュDir
 |  |  |        +- empty				[***] (ダミーファイル)
 |  |  |     +- templates_c				[777] コンパイルDir
 |  |  |        +- empty				[***] (ダミーファイル)
 |  |  +- views							[755] 
 |  |     +- layouts					[755] 
 |  |     |  +- smarty					[755] 
 |  |     |     +- mylayouts.tpl		[644] サンプルレイアウト
 |  |     +- helpers					[755] 
 |  |     |  +- smarty.php				[644] ヘルパーサンプル
 |  |     +- mypages					[755] 
 |  |     |  +- smarty					[755] 
 |  |     |     +- index.tpl			[644] サンプルindexアクション
 |  |     +- smarty.php					[644] SmartyView本体
 |  +- vendors							[755] 
 |     +- smarty						[755] 
 |        +- plugins					[755] 
 |           +- empty					[***] (ダミーファイル)
 |        +- Smarty.class.php			}※Smartyは別途ダウンロードし、
 |        ...							}　libs以下をここに配置してください
 |
 +- readme.txt							[***] このファイル

--------------------------------------------------
■設定
--------------------------------------------------

　SmartyViewクラス内での設定は特にありません。
　利用するコントローラクラスで、viewプロパティに'Smarty'と設定するだけです。

class MypagesController extends AppController {
	var $name = 'Mypages';
	var $view = 'Smarty';		//←Smarty(View)であることを指定
	var $layout = 'mylayout';
	
	//indexアクション
	//
	function index(){
	}
}

　上記の場合、/cake/app/views/layouts/smarty/mylayout.tplファイルを
レイアウトファイルとし、indexアクションにおいて
/cake/app/views/mypages/smarty/index.tplをテンプレートファイルとして使用
します。

　また、ヘルパー内でbeforeRender()、afterRender()を設定すると、
SmartyView内でコールバック処理を行います。
　さらに、beforeSmartyRender・afterSmartyRenderメソッドを設定するとSmarty
オブジェクトを受け取ってコールバック処理をすることが出来ます(後述参照)。

--------------------------------------------------
■SmartyView拡張機能
--------------------------------------------------

　標準Viewと違い、SmartyViewには次の拡張機能があります。

【beforeSmartyRenderおよびafterSmartyRenderコールバック機能】

　標準Viewでは、Helper内に「beforeRender/beforeLayout/afterLayout/
afterRender」コールバックを付加することで、レンダリング前及び後に処理を
行うことが出来ますが、この代わりに「beforeSmartyRender/beforeSmartyLayout/
afterSmartyLayout/afterSmartyRender」コールバックを設置することで、
Smartyオブジェクトのリファレンスを受け取って処理することが出来ます。

class MyHelper extends Helper {

	function beforeSmartyRender(&$smarty){
		//...
	}
	
	function beforeSmartyLayout(&$smarty){
		//...
	}
	
	function afterSmartyLayout(&$smarty){
		//...
	}
	
	function afterSmartyRender(&$smarty){
		//...
	}
}

※beforeRender/beforeLayout/afterLayout/afterRenderも残してあります。
　処理される順番は次の通りです。
　beforeRender → beforeSmartyRender	↓処理順
　beforeLayout → beforeSmartyLayout	↓
　afterLayout → afterSmartyLayout		↓
　afterRender → afterSmartyRender		↓


【ShiftJISにてSmartyViewを使うためのTips】

　携帯サイトなどShiftJISを用いたサイトの制作でSmartyを用いたい場合もあるかと
思いますが、Smartyデフォルトのデミリタ「{}」のままでは、文字化けをしてしまい
正しく動作させることが出来ません。
　そこで、デミリタを違うものに変更する必要が出てくるのですが、コントローラから
SmartyView内にあるSmartyインスタンスに直接アクセスすることは出来ません。

　そこで、自前でヘルパーを用意することで、コントローラ内でSmartyインスタンスに
直接デミリタを設定することで、この問題を解決することが出来ます。

class SmartyHelper extends Helper {

	function beforeSmartyRender(&$smarty){
		
		//Smartyデミリタの変更
		//
		$smarty->left_delimiter = '{{';
		$smarty->right_delimiter = '}}';
	}

}

※なお、上記２点に関しましてはサンプルも用意しましたので、サンプルファイルの
　方を参考にしてください。


--------------------------------------------------
■ご意見・ご感想・不具合報告など
--------------------------------------------------

　ご意見、ご感想などは、当方のブログ内専用サイトのコメント欄に書き込んで
いただけますと幸いです。

▼ECWorks
http://www.ecworks.jp/

▼ECWorks blog
http://blog.ecworks.jp/

▼SmartyViewサポートページ
http://blog.ecworks.jp/smartyview

【CM】
　テンプレートファイルを容易に作成・配置するためのツール「Tplcutter」も
公開しています。特にDreamweaver等のサイトデザインツールを利用しての制作に
大変便利です。こちらも是非ご利用ください！

--------------------------------------------------
■バージョン情報
--------------------------------------------------

【Ver1.2.4.8284】2009.08.10
　CakePHP1.2.4.8286 Stable対応バージョン。

【Ver1.2.1.8004】2009.02.25
　CakePHP1.2.1.8004 Stable対応バージョン。
　・beforeSmartyLayout/afterSmartyLayoutコールバックを新設しました。
　・RC2と比べ、キャッシュの呼ばれ方が若干異なっています(view.php由来)。
　・RC2と比べ、beforeRender/beforeLayout/afterLayout/afterRenderの呼ばれ方が
　　若干異なっています(view.php由来)。
　・サンプルテンプレートを修正しました。
　・サンプルヘルパーを追加しました。

【Ver1.2.0.7296】2008.07.07
　CakePHP1.2.0.7296(RC2)対応バージョン。
　・RC1と比べ、helperの呼ばれ方が若干異なっています(view.php由来)。
　・Cache:viewが保存されなくなりました(view.php由来)。
　・サンプルテンプレートを修正しました。

【Ver1.2.0.7119】2008.06.25
　CakePHP1.2.0.7311(RC1)対応バージョン。
　・Vendor関数が廃止となったため、App:import()にて対応しました。
　・webserviceが廃止となったため、_getViewFileName()メソッドおよび
　　_getLayoutFileName()メソッドを削除しました。
　・Viewキャッシュに対応しました(Smartyキャッシュは無効となっています)。

【Ver1.2.0.6311】2008.06.25
　CakePHP1.2.0.6311(beta)対応バージョン。
　・afterSmartyRender()コールバックが呼ばれない不具合を修正しました。
　・機能していなかったキャッシュ指定を削除しました。

【Ver1.2.0.1】2008.04.17
　・変数の参照方法が不適切だったためPHP5で不具合が出ていた箇所を修正しました。

【Ver1.2.0.0】2008.02.01
　公開バージョン
　(CakePHPのバージョンに合わせたため、それ以前のナンバーは欠番です)


**************************************************
　　ECWorks(H.N MASA-P)
　　http://www.ecworks.jp/
**************************************************
