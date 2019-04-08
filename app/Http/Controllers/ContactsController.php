<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Message;
use App\Group;
use App\GroupsMessages;
use App\UsersGroups;
use App\Events\NewMessage;
use App\Events\GroupMessage;

use Illuminate\Support\Facades\Auth;

class ContactsController extends Controller
{
    public function get()
    {
    	$contacts = User::where('id', '!=', auth()->id())->get();

        $unreadIds = Message::select(\DB::raw('`from` as sender_id, count(`from`) as messages_count'))
            ->where('to', auth()->id())
            ->where('read', false)
            ->groupBy('from')
            ->get();

        $contacts = $contacts->map(function ($contact) use ($unreadIds) {
            $contactUnread = $unreadIds->where('sender_id', $contact->id)->first();
            $contact->unread = $contactUnread ? $contactUnread->messages_count : 0;

            $to = Message::where('to', '=', $contact->id)->get()->pluck("id")->last();
            $from = Message::where('from', '=', $contact->id)->get()->pluck("id")->last();

            $contact->latest = $to > $from ? $to : $from;

            if ($contact->latest == null) {
                $contact->latest = 0;
            }

            return $contact;
        });

    	return response()->json($contacts);
    }

    public function getMessagesFor($id)
    {
    	Message::where('from', $id)->where('to', auth()->id())->update(['read' => true]);

        $messages = Message::where(function ($q) use ($id) {
            $q->where('from', auth()->id());
            $q->where('to', $id);
        })->orWhere(function($q) use ($id) {
            $q->where('from', $id);
            $q->where('to', auth()->id());
        })
        ->get();

    	return response()->json($messages);
    }

    public function getMessagesForGroup($id)
    {
        GroupsMessages::where('group_id', $id)->where('user_id', auth()->id())->update(['read' => true]);

        $messages = GroupsMessages::where('group_id', $id)->where('user_id', auth()->id())->get();

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $message = Message::create([
            'from' => auth()->id(),
            'to' => $request->contact_id,
            'text' => $request->text
        ]);

        broadcast(new NewMessage($message));

        return response()->json($message);
    }

    
    public function groupSend(Request $request)
    {
        $groupmsg = GroupsMessages::create([
            'group_id' => $request->group_id,
            'user_id' => auth()->id(),
            'text' => $request->text
        ]);

        broadcast(new GroupMessage(Auth::user(), $groupmsg));

        return response()->json($groupmsg);
    }


    public function createGroup(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $newGroup = Group::create([
            'name' => $request->name,
            'admin' => Auth::id(),
        ]);

        UsersGroups::create([
            'group_id' => $newGroup->id,
            'user_id' => $newGroup->admin,
        ]);

        return response()->json($request, 200);

    }

    public function getGroups()
    {
        $groups = Group::all();

        return response()->json($groups);
    }



    public function deleteGroupMsg($id)
    {
        $groupMsg = GroupsMessages::destroy($id);

        return response()->json($groupMsg, 200);
    }

    public function deleteMsg($id)
    {
        $msg = Message::destroy($id);

        return response()->json($msg, 200);
    }

    public function getGroupInfo($id)
    {
        $group = Group::find($id);

        $group_users = $group->users;

        var_dump($group_users);
        
        $other_users = User::whereNotIn('id', $group_users)->get();

        return response()->json([
            'group_users' => $group_users,
            'other_users' => $other_users
        ]);
    }

    public function deleteGroup($id)
    {
        $group = Group::find($id);

        UsersGroups::where('group_id', '=', $id)->delete();
        $group->hasMessages()->delete();

        $group->delete();

        return response()->json([
            'group_users' => $group->users
        ]);

    }

    
    public function addUsersToGroup(Request $request, $id)
    {
        $group = Group::find($id);

        

        $users = $request->all();

        foreach($users as $user)
        {

            $add_user = new UsersGroups;
            $add_user->group_id = $id;
            $add_user->user_id = $user["id"];
            $add_user->save();
        }

        


    }
}
