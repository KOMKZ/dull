<?php

use yii\db\Migration;

class m170222_120127_sys_msg extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sys_msg}}', [
            'sm_id' => $this->primaryKey(),
            'sm_mid' => $this->integer()->notNull(),
            'sm_create_uid' => $this->integer()->notNull(),
            'sm_object_type' => $this->smallinteger()->notNull(),
            'sm_object_id' => $this->integer()->notNull(),
            'sm_use_tpl' => $this->smallinteger()->notNull(),
            'sm_tpl_type' => $this->string(100)->null(),
            'sm_title' => $this->string(255)->null(),
            'sm_content' => $this->text()->null(),
            'sm_expired_at' => $this->integer()->notNull(),
            'sm_created_at' => $this->integer()->notNull()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%sys_msg}}');
        return true;
    }
}
/*
# 站内信
## 使用场景
1. 发送通知给全站用户，如发送一条系统维护消息
2. 发送通知个部分用户，如推送更新消息给4000个粉丝

## 问题分析
1. 如果在发送时准备好所有的消息将造成系统缓慢，如10个用户有10000个粉丝，在同一个时间段内更新，此时需要写入10000条通知
2. 广播消息类似上面

## 解决思路
1,2 问题在于发送通知的瞬间需要将消息全部准备好，属于推送的思路，解决这个问题的思路可以使用拉取的思路：
对于广播消息：
1. 写入一条广播消息
2. 通过某个动作触发用户拉取广播消息，如登录
3. 用户拉取到广播消息，写入未读表中
4. 整体查询未读表返回

对于限定范围用户的消息而言，上述方案无法解决，由于无法知道该条消息是否属于自己，故需要额外的手段来分析该消息是否属于自己，考虑如下：
1000个用户同时做了更新动作，其中只有一个更新动作和该用户有关，则999次分析操作是浪费的，而且无法避免。且每个用户都必须这样操作，这种思路
是不可取的。

我们想要解决的问题是 如10个用户有10000个粉丝，在同一个时间段内更新，此时需要写入10000条通知， 如何减少这个时间或者这种频繁写的问题，

对于上述的场景，继续使用拉取的思路来实现，假设该有个用户正好是这10个用户的粉丝，则该用户应该要有10条通知，实现思路如下：
1. 10个用户发生更新，插入10条通知，标明用户自己
2. 通过某个动作触发用户拉取属于自己的通知，
    1. 用户想要拉取关注的人的更新通知
    2. 用户取出关注的人的列表，取出id
    3. 检索该id的通知，发现有10条，然后插入

拉取能够将10000条写入分散在不同的时间段中，但是是牺牲查询的时间来达到的。需要对通知加入过期时间来减少检索的数据量。

拉取的一个严重的问题：
许多通知需要动态的数据，如用户名的参与，拉取出某条信息时，我们无法知道该条信息需要什么动态数据？如：
拉取出一条信息如下：
```
尊敬的用户 {username}, 欢迎您成为DULL软件的用户！
```
这里{username}标注着是一个动态信息，需要在这个时间段填充进去，如果不想通过存储的方式来解决填充的问题，那么就只能通过约定，如
```
class A{
    function getUserName();
    function getPhone();
}
```


 */
