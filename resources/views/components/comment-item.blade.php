@props(['comment', 'post', 'reportReasons', 'isReply' => false])

<article {{ $attributes->merge(['class' => ($isReply ?? false)
    ? 'border-l-2 border-line-strong bg-surface p-3 transition-colors hover:border-ink'
    : 'border border-line bg-surface p-4 transition-colors hover:border-line-strong shadow-2xs']) }}>
    @if ($comment->trashed() || $comment->status === \App\Enums\CommentStatus::Hidden)
        <div class="flex items-center gap-2 py-0.5 text-xs italic text-ink-muted font-mono">
            <svg class="size-3.5 shrink-0 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
            <span>Bình luận này đã bị xóa hoặc ẩn.</span>
        </div>
    @else
        <div class="flex items-start gap-2.5 sm:gap-3">
            <!-- User Avatar -->
            <span class="grid {{ ($isReply ?? false) ? 'size-6 text-[10px]' : 'size-7 sm:size-8 text-[11px]' }} shrink-0 place-items-center bg-ink font-bold font-mono text-lime border border-ink">
                {{ mb_strtoupper(mb_substr($comment->user->name, 0, 1)) }}
            </span>

            <!-- Body & Actions -->
            <div class="min-w-0 flex-1">
                <!-- Header Line: Author + Badges + Reply-to + Timestamp -->
                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 leading-tight">
                    <span class="text-xs sm:text-[13px] font-bold text-ink">{{ $comment->user->name }}</span>

                    @if ($comment->user->id === $post->author_id)
                        <span class="border border-line-strong bg-lime px-1.5 py-0.2 text-[9px] font-bold font-mono uppercase tracking-wider text-ink shadow-2xs">
                            Tác giả
                        </span>
                    @endif

                    @if ($comment->replyTo?->user)
                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-ink-muted font-mono">
                            <span>&hookleftarrow; trả lời</span>
                            <strong class="font-bold text-ink">{{ $comment->replyTo->user->name }}</strong>
                        </span>
                    @endif

                    <span class="text-[10px] text-ink-muted font-mono">
                        {{ $comment->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                <!-- Content -->
                <p class="mt-1.5 whitespace-pre-line text-xs sm:text-[13px] leading-relaxed text-ink">
                    {{ $comment->content }}
                </p>

                <!-- Actions Row (Compact) -->
                @auth
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs font-mono text-ink-muted">
                        @if (auth()->user()->hasVerifiedEmail())
                            <details class="group/reply">
                                <summary class="inline-flex cursor-pointer items-center gap-1 font-bold text-ink hover:underline select-none transition-colors">
                                    <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Trả lời</span>
                                </summary>
                                <form method="POST" action="{{ route('comments.store', $post) }}" class="mt-2 flex flex-col sm:flex-row gap-1.5">
                                    @csrf
                                    <input type="hidden" name="reply_to_id" value="{{ $comment->id }}">
                                    <input name="content" required maxlength="2000" placeholder="Trả lời {{ $comment->user->name }}..." class="min-w-0 flex-1 border border-line bg-paper px-2.5 py-1 text-xs text-ink outline-none focus:border-line-strong focus:shadow-brutal-sm">
                                    <button type="submit" class="inline-flex min-h-7 cursor-pointer items-center justify-center border border-line-strong bg-lime px-3 py-1 text-xs font-bold text-ink shadow-brutal-sm hover:bg-lime-hover active:scale-[0.98]">
                                        Gửi
                                    </button>
                                </form>
                            </details>
                        @endif

                        @can('update', $comment)
                            <details class="group/edit">
                                <summary class="inline-flex cursor-pointer items-center gap-1 hover:text-ink select-none transition-colors">
                                    <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    <span>Sửa</span>
                                </summary>
                                <form method="POST" action="{{ route('comments.update', $comment) }}" class="mt-2 flex flex-col sm:flex-row gap-1.5">
                                    @csrf
                                    @method('PUT')
                                    <input name="content" value="{{ $comment->content }}" required maxlength="2000" class="min-w-0 flex-1 border border-line bg-paper px-2.5 py-1 text-xs text-ink outline-none focus:border-line-strong focus:shadow-brutal-sm">
                                    <button type="submit" class="inline-flex min-h-7 cursor-pointer items-center justify-center border border-line-strong bg-lime px-3 py-1 text-xs font-bold text-ink shadow-brutal-sm hover:bg-lime-hover active:scale-[0.98]">
                                        Lưu
                                    </button>
                                </form>
                            </details>
                        @endcan

                        @can('delete', $comment)
                            <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                  data-confirm="Bạn có chắc chắn muốn xóa bình luận này không?"
                                  data-confirm-title="Xóa bình luận"
                                  data-confirm-type="danger"
                                  data-confirm-btn="Xóa ngay"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex cursor-pointer items-center gap-1 text-danger hover:underline transition-colors font-bold">
                                    <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Xóa</span>
                                </button>
                            </form>
                        @endcan

                        @can('report', $comment)
                            <details class="group/report">
                                <summary class="inline-flex cursor-pointer items-center gap-1 hover:text-ink select-none transition-colors">
                                    <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Báo cáo</span>
                                </summary>
                                <form method="POST" action="{{ route('comment-reports.store', $comment) }}" class="mt-2 grid min-w-60 max-w-sm gap-2 border-2 border-line-strong bg-surface p-3 shadow-brutal font-sans">
                                    @csrf
                                    <label class="text-[10px] font-bold font-mono uppercase tracking-wider text-ink">Lý do báo cáo:</label>
                                    <select name="reason" required class="border border-line bg-paper px-2 py-1 text-xs text-ink outline-none focus:border-line-strong">
                                        @foreach ($reportReasons as $reason)
                                            <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                                        @endforeach
                                    </select>
                                    <textarea name="description" maxlength="1000" rows="2" placeholder="Mô tả cụ thể vi phạm (tùy chọn)..." class="border border-line bg-paper p-2 text-xs text-ink placeholder:text-ink-muted outline-none focus:border-line-strong"></textarea>
                                    <button type="submit" class="inline-flex min-h-7 cursor-pointer items-center justify-center border border-line-strong bg-ink px-3 py-1 text-xs font-bold text-lime shadow-brutal-sm hover:bg-lime hover:text-ink active:scale-[0.98] transition-colors">
                                        Gửi báo cáo
                                    </button>
                                </form>
                            </details>
                        @endcan
                    </div>
                @endauth
            </div>
        </div>
    @endif
</article>
