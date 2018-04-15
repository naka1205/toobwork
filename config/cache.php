<?php
/* 
 * 缓存配置文件 
 */ 
return [
        'type'   => 'file',			// 驱动方式
        'path'   => CACHE_PATH,	// 缓存保存目录
        'prefix' => 'too',			// 缓存前缀
        'expire' => 0,			// 缓存有效期 0表示永久缓存
];