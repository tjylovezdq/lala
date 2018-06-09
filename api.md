> 采集文章 lala.test/api/query/get-query-articles
- GET
- params

参数名|类型|注释
---|---|---
category_id|int|类型 1,2,3,4,5,6
- 返回
```
成功
```

> 审核文章列表 lala.test/api/article/listing
- GET
- params `无`
```
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "user_id": 0,
            "title": "关于项链的文章",
            "cover": "p8to9e2ei.bkt.clouddn.com/201806082132335575.jpg",
            "type": 0,
            "status": 0,
            "category_id": 1,
            "show_num": 0,
            "create_time": "2018-06-08 16:00:38",
            "operate_time": "2018-01-01 00:00:00",
            "update_time": "2018-06-08 16:00:38"
        },
        {
            "id": 2,
            "user_id": 0,
            "title": "认识自己的文章",
            "cover": "p8to9e2ei.bkt.clouddn.com/201806062114359475.jpg",
            "type": 0,
            "status": 0,
            "category_id": 1,
            "show_num": 0,
            "create_time": "2018-06-08 16:00:39",
            "operate_time": "2018-01-01 00:00:00",
            "update_time": "2018-06-08 16:00:39"
        }
    ],
    "first_page_url": "http://lala.test/api/article/listing?page=1",
    "from": 1,
    "last_page": 60,
    "last_page_url": "http://lala.test/api/article/listing?page=60",
    "next_page_url": "http://lala.test/api/article/listing?page=2",
    "path": "http://lala.test/api/article/listing",
    "per_page": 2,
    "prev_page_url": null,
    "to": 2,
    "total": 120
}
```

