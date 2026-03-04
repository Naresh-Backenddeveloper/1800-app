1. User Login 
POST:/auth/login
  Headers: Content-Type: application/json   
  request:{
    'mobile':< User Mobile num>
  }       
  Response:{
                "code": 0,
                "message": "OK"
            } 

2. Verfy Otp 
POST:/auth/verfy
Headers:Content-Type: application/json 
 request:{
            'mobile':< User Mobile num>,
            'otp':<>,
        }  
  response:{
            "code": 0,
            "message": "OK",
            "data": {
                "token": "<Token of the user>",
                "profile_flag": <Flage of 1-0> 1- already signedup , 0- new user need to signup
            }
         }   

 3. Get Profile
 GET:/secure/profile    
 request:{}       
 Response:{
    {
    "code": 0,
    "message": "OK",
    "data": {
        "id": <>,
        "name": "<>",
        "email": "<>",
        "email_verified_at": <>,
        "created_at": "<>",
        "updated_at": "<>",
        "mobile": "<>",
        "active_flag": <>,
        "role": "<>",
        "address": <>,
        "latitude": <>,
        "longitude": <>,
        "otp_expires_at": "<>",
        "otp": "<>",
        "profile_flag": <>,
        "verify_flag": <>,
        "profile_pic": "<>"
    }
}

 }

4. Update or Sigup Profile
POST:/secure/profile/update\

Request:{
    'profile_image' => '<>',
            'username' => '<>',
            'email' => '<>',
            'location' => '<nullable>',
            'latitude' => '<nullable>',
            'longitude' => '<nullable>'
}

Response:{
    Code:0/1,
    message:Ok/ Inavlid
}

5. Category List
GET:/secure/home/category
request:{}
response:{
    "code": 0,
    "message": "Categories retrieved successfully",
    "count": 4,
    "data": [
        {
            "id": <>,
            "name": "<>",
            "icon_url": "<>",
            "subcategories": [
                {
                    "id": <>,
                    "name": "<>",
                    "icon_url": "<>"
                },......
            ],
            "specifications": [
                {
                    "id": <>,
                    "specification": "<>"
                },...
            ]
        },
    ]
}

6. Fresh Recommendations
GET:/secure/home/fresh-recommendations
request:{}
Response:{
   "code": 0,
    "message": "Loaded 1 boosted + 1 normal active products",
    "data": {
        "boosted": [
            {
                "id": <>,
                "title": "<>",
                "price_display": "<>",
                "main_image_url": "<>",
                "location": "<>",
                "time_ago": "<>",
                "is_favorited": <>,
                "is_boosted": <>,
                "boost": {
                    "package_title": "<>",
                    "package_slug": <>,
                    "priority_label": "<>",
                    "expired_at": <>,
                    "expires_in": <>
                },
                "status": "<>"
            }
        ],
        "normal": [
            {
                "id": <>,
                "title": "<>",
                "price_display": "<>",
                "main_image_url": "<>",
                "location": "<>",
                "time_ago": "<>",
                "is_favorited": <>,
                "is_boosted": <>,
                "boost": <>,
                "status": "<>"
            }
        ]
    },
    "stats": {
        "boosted_count": <>,
        "normal_count": <>,
        "total": <>
    }
}

7. Category products 
GET:secure/home/category/adds/{subCategoryId}
request:{}

response:{
     "code": 0,
    "message": "<>",
    "category_id": <>,
    "sort": "<>",
    "data": {
        "page": <>,
        "has_next": <>,
        "per_page": <>,
        "total": <>,
        "data": [
            {
                "id": <>,
                "title": "<>",
                "price": "<>",
                "price_display": "<>",
                "main_image_url": "<>",
                "location": "<>",
                "time_ago": "<>",
                "is_favorited": <>,
                "is_boosted": <>,
                "boost": {
                    "package_title": "<>",
                    "package_slug": <>,
                    "priority_label": "<>",
                    "expired_at": "<>",
                    "expires_in": "<>"
                },
                "specifications": [
                    {
                        "key": "<>",
                        "label": "<>",
                        "value": "<>"
                    },...
                    
                ]
            },.....
        ]
    }
}

8. Product  Details
GET:/secure/adds/product/{ProductId}

Request:{}
Response:{
    {
    "code": 0,
    "message": "Product details retrieved successfully",
    "data": {
        "id": <>,
        "title": "<>",
        "description": "<>",
        "price": "<>",
        "price_display": "<>",
        "category_id": <>,
        "category_name": "<>",
        "main_image_url": "<>",
        "location": "<>",
        "city": "<>",
        "sellerName": "<>",
        "phone": "<>",
        "whatsapp": "<>",
        "email": "<>",
        "condition": "<>",
        "negotiable": <>,
        "specifications": [
            {
                "key": "<>",
                "label": "<>",
                "value": "<>"
            },...
        ],
        "created_at": "2026-02-27 10:55:44",
        "time_ago": "2 days ago",
        "views": 0,
        "is_favorited": false
    }
}
}

9. States
GET:auth/states

request:{}
response:{
    {
    "code": 0,
    "message": "OK",
    "data": [
        {
            "state_id": <>,
            "state_name": "<>",
            "active_flag": <>
        },....
    ]
    }
}

10. Districts
GET:auth/district
Request:{}
response:{
    {
    "code": 0,
    "message": "OK",
    "data": [
        {
            "district_id": <>,
            "district_name": "<>",
            "state_id": <>,
            "active_flag": <>
        },..
    ]
    }
}


11. My post and Adds 

GET:secure/adds

request:{}
response:{
    "code": 0,
    "message": "2 products loaded (1 boosted + 1 normal)",
    "data": {
        "tab": "all",
        "counts": {
            "all": 0,
            "boosted": 1,
            "normal": 1,
            "active": 0,
            "pending": 0,
            "sold": 0,
            "expired": 0
        },
        "total": 2,
        "boosted": [
            {
                "id": 1,
                "title": "Tata Togo Z",
                "slug": null,
                "price": 120000,
                "price_display": "INR 120,000",
                "main_image_url": "http://127.0.0.1:8000/cloud/http://127.0.0.1:8000/cloud/images/product_image1.jpeg",
                "status": "ACTIVE",
                "status_color": "#22c55e",
                "views_count": 1,
                "favorites_count": 1,
                "posted_at": "2026-02-27 10:55:44",
                "posted_ago": "3 days ago",
                "is_boosted": true,
                "boost": {
                    "package_title": "Unknown",
                    "package_slug": null,
                    "price_paid": "55.00",
                    "expired_at": "2026-04-02 10:55:55",
                    "expires_in": "4 weeks",
                    "boost_priority": "unknown"
                },
                "location": "HYDERABAD",
                "condition": "GOOD",
                "year": 2015
            }
        ],
        "normal": [
            {
                "id": 2,
                "title": "Iphone 15 Pro",
                "slug": null,
                "price": 90000,
                "price_display": "INR 90,000",
                "main_image_url": null,
                "status": "ACTIVE",
                "status_color": "#22c55e",
                "views_count": 1,
                "favorites_count": 0,
                "posted_at": "2026-02-27 10:55:44",
                "posted_ago": "3 days ago",
                "is_boosted": false,
                "boost": null,
                "location": "HYDERABAD",
                "condition": "GOOD",
                "year": 2020
            }
        ]
    }
}

12. My Favourites
GET:secure/adds/favorites

request:
response:{
    "code": 0,
    "message": "Loaded 1 favorite products (1 boosted + 0 normal)",
    "total": 1,
    "boosted_count": 1,
    "normal_count": 0,
    "data": {
        "boosted": [
            {
                "id": 1,
                "title": "Tata Togo Z",
                "price": 120000,
                "price_display": "₹120,000 (Negotiable)",
                "main_image_url": "http://127.0.0.1:8000/cloud/images/product_image1.jpeg",
                "location": "HYDERABAD",
                "time_ago": "3 days ago",
                "is_favorited": true,
                "is_boosted": true,
                "boost": {
                    "package_title": "Boosted",
                    "package_slug": null,
                    "priority_label": "Boosted",
                    "expired_at": "2026-04-02 10:55:55",
                    "expires_in": "4 weeks"
                },
                "status": "ACTIVE",
                "condition": "GOOD",
                "year": 2015,
                "views_count": 1
            }
        ],
        "normal": [
            {
                "id": 2,
                "title": "Iphone 15 Pro",
                "price": 90000,
                "price_display": "₹90,000 (Negotiable)",
                "main_image_url": null,
                "location": "HYDERABAD",
                "time_ago": "3 days ago",
                "is_favorited": true,
                "is_boosted": false,
                "boost": null,
                "status": "ACTIVE",
                "condition": "GOOD",
                "year": 2020,
                "views_count": 1
            }
        ]
    }
}

13. ADD Post 
POST:/secure/adds/add
request:{
    'product_images'     => 'required',
            'product_images.*'   => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'title'              => 'required',
            'category_id'        => 'required',
            'sub_category_id'    => 'required',
            'specifications'     => 'required|json',
            'condition'          => 'required',
            'description'        => 'required',
            'price'              => 'required',
            'price_negotiable'   => 'required',
            'location'           => 'required',
            'city'               => 'nullable',
            'latitude'           => 'nullable',
            'longitude'          => 'nullable',
            'year'               => 'nullable
}

response:{
    {
    "code": 0,
    "message": "Ad posted successfully with 4 image(s). Under review.",
    "data": {
        "product_id": 3,
        "title": "Samsung s 24 Ultra",
        "status": "pending"
    }
}
}
14. Subscription List
GET:/secure/adds/subscription
request:{}
Response:{
    "code": 0,
    "message": "OK",
    "data": [
        {
            "id": 1,
            "title": "Basic Boost",
            "price": "49.00",
            "icon": "images/car_icon.jpg",
            "slug": "Premimum",
            "duration_days": 45,
            "view_multiplier": 2,
            "features": null,
            "badge_text": "popular",
            "active_flag": true,
            "sort_order": null,
            "created_at": "2026-02-03T10:55:50.000000Z",
            "updated_at": "2026-02-03T10:55:50.000000Z"
        },...
    ]
}


15.  POST  Subscriptions
POST:secure/adds/subscription/{postId}

request:{
"subscription_id":<>
"price":<>
}

response:{
    {
    "code": 0,
    "message": "Boost extended successfully!",
    "data": {
        "product_id": 3,
        "package_title": "Basic Boost",
        "package_slug": "Premimum",
        "price": "49.00",
        "duration_days": 45,
        "expired_at": "2026-07-16 10:58:01",
        "expires_in": "4 months left",
        "action": "extended"
    }
}
}

16. delete Image of Post
GET:secure/adds/delete/{ImageId}->Image Id From The product Details
request:{}
response:{
    {
    "code": 0,
    "message": "Product Image Removed"
}
}

17. Upload New Images Of Post
POST:secure/adds/images/{PostID}
request:{
    product_images:<In array>
}
response:{
    "code": 0,
    "message": "Successfully added 2 new image(s)",
    "data": {
        "product_id": "3",
        "images_added": 2,
        "has_main_image": true
    }
}

18. edit Post Details 
POST:secure/adds/edit/{postId}

request:{
            'title'              => 'required',
            'category_id'        => 'required',
            'sub_category_id'    => 'required',
            'specifications'     => 'required',
            'condition'          => 'required',
            'description'        => 'required',
            'price'              => 'required',
            'price_negotiable'   => 'required',
            'location'           => 'required',
            'city'               => 'nullable',
            'latitude'           => 'nullable',
            'longitude'          => 'nullable',
            'year'               => 'nullable'
}
response:{
    "code": 0,
    "message": "Product Updated"
}

19. Make Favorite Post
GET:secure/adds/favorite/add/{PostId}
request:{}
response:{
    "code": 0,
    "meassge": "OK"
}

20. remove favorite
GET:secure/adds/favorite/remove/{PostId}
request:{}
response:{
    "code": 0,
    "meassge": "OK"
}

21. My chats
GET:secure/mychats
request:{}
response:{
    "code": 0,
    "chats": [
        {
            "chat_id": 2,
            "type": "as_buyer",
            "product": {
                "id": 2,
                "title": "Iphone 15 Pro"
            },
            "seller": {
                "id": 5,
                "name": "Admin"
            },
            "last_message": "Is it still Avaliable",
            "last_time": "14 minutes ago",
            "updated_at": "2026-03-03T12:33:52.000000Z"
        },
        {
            "chat_id": 1,
            "type": "as_seller",
            "product": {
                "id": 3,
                "title": "Samsung s 25 Ultra"
            },
            "seller": {
                "id": 5,
                "name": "Admin"
            },
            "last_message": null,
            "last_time": "17 minutes ago",
            "updated_at": "2026-03-03T12:30:52.000000Z"
        }
    ],
    "stats": {
        "total": 2,
        "as_buyer": 1,
        "as_seller": 1
    }
}

22. Product Chat On click
GET:secure/chat/{PostId}
request:{}
response:{
    {
    "code": 0,
    "message": "OK",
    "chatId": 2,
    "sellerId": 5,
    "productTitle": "Iphone 15 Pro"
}
}

23. messages of Chat
GET:secure/messages/{ChatId}
request:{}
response:{
    "code": 0,
    "chat": {
        "id": 2,
        "buyer_id": 1,
        "seller_id": 5
    },
    "messages": [
        {
            "id": 1,
            "sender_id": 1,
            "isMine": true,
            "message": "Is it still Avaliable",
            "created_at": "2026-03-03T12:33:52.000000Z",
            "read": false
        },
        {
            "id": 2,
            "sender_id": 5,
            "isMine": false,
            "message": "Yes It avliable",
            "created_at": "2026-03-03T18:24:52.000000Z",
            "read": false
        }
    ]
}

24. Send Message
POST:/secure/send/messages/{chatid}

request:{}
response:{
    "code": 0,
    "message": "Message sent successfully",
    "data": {
        "id": 3,
        "chat_id": 2,
        "sender_id": 1,
        "is_mine": true,
        "message": "I will Buy",
        "created_at": "2026-03-03T12:58:18+00:00",
        "read": false
    }
}

25. product Chat Request Users
GET:/secure/requestedusers/{ProductId}

request:{}
response:{
    {
    "code": 0,
    "message": "Success",
    "data": {
        "product": {
            "id": 3,
            "title": "Samsung s 25 Ultra"
        },
        "interested_users": [
            {
                "user_id": 5,
                "name": "Admin"
            }
        ],
        "count": 1
    }
}
}

26. Make it slod 
POST:/secure/sold/{productId}
request:{
    "sold_to":<USERID>,
    "rating":<1-5>
}
response:{
    "code": 0,
    "message": "Product marked as sold successfully",
    "data": {
        "product_id": 3,
        "title": "Samsung s 25 Ultra",
        "sold_to": "5",
        "sold_at": "2026-03-04 05:25:17",
        "status": "SOLD",
        "active_flag": 0
    }
}


27. firebase message
POST:/secure/save-fcm-token
request:{
    "fcm_token":<>
}'
response:{
    code:0,
    message:OK
}